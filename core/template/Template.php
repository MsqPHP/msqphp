<?php declare(strict_types = 1);
namespace msqphp\core\template;

use msqphp\base;
use msqphp\core;
use msqphp\traits;

final class Template
{
    private static $left = '';
    private static $right = '';
    private static $left_delim = '';
    private static $right_delim = '';

    private static $pattern = [];
    private static $inited = false;

    private static function init()
    {
        $config              = core\config\Config::get('template');
        static::$left_delim  = $config['left_delim'] ?? '<{';
        static::$right_delim = $config['right_delim'] ?? '<{';
        static::$left        = $left = '/'.static::$left_delim.'\\s*';
        static::$right       = $right = '\\s*'.static::$right_delim.'\\s*/';

        $blank     = '\s+';
        $may_blank = '\s*';
        $var       = '\$([A-Za-z_][A-Za-z0-9_]*)';
        $compare   = '(\<\=|\>=|\<|\>|\<\>|\!\=|\=\=|\=\=\=|\!\=\=)';

        static::$pattern = [
            'include'     => [ 'pattern' => $left.'include'.$blank.'([\w\/\.:-]+)'.$right ],
            'constant_a'  => [ 'pattern' => $left.'constant\.([A-Za-z_]+)'.$right ],
            'constant_b'  => [ 'pattern' => $left.'cont\.([A-Za-z_]+)'.$right ],
            'language_a'  => [ 'pattern' => $left.'language\.([A-Za-z_]+)'.$right ],
            'language_b'  => [ 'pattern' => $left.'lang\.([A-Za-z_]+)'.$right ],
            'var'         => [ 'pattern' => $left.$var.$right ],
            'array'       => [ 'pattern' => $left.$var.'([\[\w\'\"\]]+)'.$right ],
            //形式A       foreach $array as $value
            'foreach_a'   => [
                                'pattern'=>$left.'foreach'.$blank.$var.$blank.'as'.$blank.$var.$right,
                                'replace'=>''
                            ],
            //形式B       foreach $array as $key => $value
            'foreach_b'   => [
                                'pattern'=>$left.'foreach'.$blank.$var.$blank.'as'.$blank.$var.'\s*\=\>\s*\$([A-Za-z_][A-Z0-9a-z_]*)'.$right,
                                'replace'=>''
                            ],
            //正则
            'foreach_end' => [
                                'pattern'=>$left.'(\/endforeach|endforeach)'.$right,
                                'replace'=>''
                            ],
            'if_a'        => [
                                'pattern'=>'if'.$blank.$var.$may_blank.$compare.$may_blank.$var.'',
                                'replace'=>'<?php if(\$\\1\\2$\\3) : ?>'
                            ],
            'if_b'        => [
                                'pattern'=>'if'.$blank.$var.$may_blank.$compare.$may_blank.'([\'][^\']*[\']|[\"][^\"]*[\"]|[0-9])',
                                'replace'=>'<?php if(\$\\1\\2\\3) : ?>'
                            ],
            'elseif_a'    => [
                                'pattern'=>'elseif'.$blank.$var.$may_blank.$compare.$may_blank.$var.'',
                                'replace'=>'<?php elseif(\$\\1\\2$\\3) : ?>'
                            ],
            'elseif_b'    => [
                                'pattern'=>'elseif'.$blank.$var.$may_blank.$compare.$may_blank.'([\'][^\']*[\']|[\"][^\"]*[\"]|[0-9])',
                                'replace'=>'<?php elseif(\$\\1\\2\\3) : ?>'
                            ],
            'else'        => [
                                'pattern'=>$left.'else'.$right,
                                'replace'=>'<?php else : ?>',
                            ],
            'endif'       => [
                                'pattern'=>$left.'(\/endif|endif)'.$right,
                                'replace'=>'<?php endif ;?>',
                            ],
        ];

    }
    public static function commpile(string $content, array $data = [], array $language = []) :string
    {
        if (!static::$inited) {
            static::init();
        }


        $left_delim = static::$left_delim;
        $right_delim = static::$right_delim;
        $right_delim_len = strlen($right_delim);


        $content_arr = [];


        while (false !== $start = strpos($content, $left_delim)) {
            if ($start !== 0) {
                $content_arr[] = substr($content, 0, $start);
                $content = substr($content, $start);
                $start = 0;
            }
            $end = strpos($content, $right_delim);
            $content_arr[] = substr($content, 0, $end + $right_delim_len);
            $content = substr($content, $end + $right_delim_len);
        }
        !empty($content) && $content_arr[] = $content;

        $result = '';

        while (isset($content_arr[0])) {
            if (base\str\Str::startsWith($content_arr[0], $left_delim) && base\str\Str::endsWith($content_arr[0], $right_delim)) {
                $tag = $content_arr[0];
                if (base\str\Str::startsWith($tag, $left_delim.'foreach')) {
                    $deep = 1;
                    $foreach_content = $content_arr[0];
                    for ($i = 1, $l = count($content_arr); $i < $l; ++$i) {
                        if (base\str\Str::startsWith($content_arr[$i],$left_delim.'foreach')) {
                            ++$deep;
                        }
                        if (base\str\Str::startsWith($content_arr[$i],[$left_delim.'endforeach', $left_delim.'/endforeach'])) {
                            --$deep;
                        }
                        $foreach_content .= $content_arr[$i];
                        if ($deep === 0) {
                            break;
                        }
                    }
                    $result .= static::parForeach($foreach_content, $data, $language);
                    unset($foreach_content);
                    for (; $i >= 0; --$i) {
                        array_shift($content_arr);
                    }
                } else {
                    $result .= static::parOne($tag, $data, $language);
                    array_shift($content_arr);
                }
            } else {
                $result .= $content_arr[0];
                array_shift($content_arr);
            }
        }

        return $result;
    }
    private static function parOne(string $content, array $data, array $language = []) : string
    {
        //先包含文件
        $content = static::parInclude($content, $data, $language);
        //解析常量
        $content = static::parConstant($content);
        $content = static::parLanguae($content, $language);
        $content = static::parVar($content, $data);
        return static::parArray($content,$data);
    }
    private static function parFor(string $content, array $data) : string
    {
        return $content;
    }
    private static function parSwitch(string $content, array $data) : string
    {
        return $content;
    }
    /**
     * 解析包含文件
     * @example <{include 'file.html'}>  =>    file_get_contents(file.html);
     */
    private static function parInclude(string $content, array $data, array $language) : string
    {
        return preg_replace_callback(
            static::$pattern['include']['pattern'],
            function($matches){
                $file = $matches[1];
                if (is_file($file)) {
                    return static::commpile(base\file\File::get($file), $data, $language);
                } else {
                    throw new TemplateException($file.'模版文件不存在');
                }
            },
            $content
        );
    }
    /**
     * 解析常量
     * @example <{constant.IMAGE}>  =>    http://image.test.com/ (一次解析, 直接替换)
     */
    private static function parConstant(string $content) : string
    {
        return preg_replace_callback(
            [
                static::$pattern['constant_a']['pattern'],
                static::$pattern['constant_b']['pattern']
            ],
            function($matches) {
                if (defined($matches[1])) {
                    return constant($matches[1]);
                } else {
                    throw new TemplateException($matches[1].'常量未定义');
                }
            },
            $content
        );
    }
    /**
     * 解析语言
     * @example <{lang.username}>  =>    用户名 | username (一次解析, 直接替换)
     */
    private static function parLanguae(string $content, array $language) : string
    {
        return preg_replace_callback(
            [
                static::$pattern['language_a']['pattern'],
                static::$pattern['language_b']['pattern']
            ],
            function($matches) use ($language) {
                if (!isset($language[$matches[1]])) {
                    throw new TemplateException($matches[1].'对应语言不存在');
                }
                return $language[$matches[1]];
            },
            $content
        );
    }
    /**
     * 解析变量
     * @example         <{$name}>      -->  <?php echo $name;?>
     * @example (cache) <{$name}>      -->  value
     */
    private static function parVar(string $content, array $data) : string
    {
        return preg_replace_callback(
            static::$pattern['var']['pattern'],
            function($matches) use ($data) {
                $key = $matches[1];
                if (isset($data[$key]) && $data[$key]['cache']) {
                    if (is_array($data[$key]['value'])) {
                        throw new TemplateException($key.'数组被当作普通变量使用');
                    }
                    return $data[$key]['value'];
                } else {
                    return '<?php echo $'.$key.';?>';
                }
            },
            $content
        );
    }
    private static function parArray(string $content, array $data) : string
    {
        return preg_replace_callback(
            static::$pattern['array']['pattern'],
            function($matches) use ($data) {
                $key = $matches[1];
                $val = $matches[2];
                if (isset($data[$key]) && $data[$key]['cache']) {

                    $arr_key = array_map(
                                    function ($value) {
                                        if (0 !== preg_match('/^\d+$/', $value)) {
                                            return (int) $value;
                                        } else {
                                            return trim($value, '\'"');
                                        }
                                    },
                                    explode('][', trim($val, '[]'))
                                );

                    switch (count($arr_key)) {
                        case 1:
                            $result = $data[$key]['value'][$arr_key[0]];
                            break;
                        case 2:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]];
                            break;
                        case 3:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]];
                            break;
                        case 4:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]][$arr_key[3]];
                            break;
                        case 5:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]][$arr_key[3]][$arr_key[4]];
                            break;
                        case 6:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]][$arr_key[3]][$arr_key[4]][$arr_key[5]];
                            break;
                        case 7:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]][$arr_key[3]][$arr_key[4]][$arr_key[5]][$arr_key[6]];
                            break;
                        case 8:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]][$arr_key[3]][$arr_key[4]][$arr_key[5]][$arr_key[6]][$arr_key[7]];
                            break;
                        case 9:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]][$arr_key[3]][$arr_key[4]][$arr_key[5]][$arr_key[6]][$arr_key[7]][$arr_key[8]];
                            break;
                        case 10:
                            $result = $data[$key]['value'][$arr_key[0]][$arr_key[1]][$arr_key[2]][$arr_key[3]][$arr_key[4]][$arr_key[5]][$arr_key[6]][$arr_key[7]][$arr_key[8]][$arr_key[9]];
                            break;
                        default:
                            throw new TemplateException('数组维数过多');
                    }
                    return $result;
                } else {
                    return '<?php echo $'.$key.$val.';?>';
                }
            },
            $content
        );
    }
    /**
     * 解析foreach
     * @example $array = ['hello', 'world');
     * @example <{foreach $array as $value}>   -->       <?php foreach($array as $value):?>
     *              <{$v}>                     -->       <?php echo $v;?>
     *          <{endforeach}>                 -->       <?php endforeach;?>
     * @example <{foreach $array as $value}>   -->       ''
     *              <{$v}>                     -->
     *          <{endforeach}>                 -->       <?php endforeach;?>
     *
     */
    private static function parForeach(string $content, array $data, array $language) : string
    {
        if (0 === preg_match(static::$pattern['foreach_end']['pattern'], $content, $endforeach)) {
            throw new TemplateException('未闭合的foreach标签');
        }
        if (0 !== preg_match(static::$pattern['foreach_a']['pattern'], $content, $foreach)) {
            $type = 'a';
        } elseif(0 !== preg_match(static::$pattern['foreach_b']['pattern'], $content, $foreach)) {
            $type = 'b';
        } else {
            throw new TemplateException('错误的foreach语法');
        }
        $array_key = $foreach[1];
        $endforeach_content = $endforeach[0];

        if (isset($data[$array_key]) && $data[$array_key]['cache']) {
            $foreach_content = $foreach[0];
            $foreach_len     = strlen($foreach_content);
            $foreach_pos     = strpos($content, $foreach_content);
            $endforeach_len  = strlen($endforeach_content);
            $endforeach_pos  = strpos($content, $endforeach_content);
            $all_foreach     = substr($content, $foreach_pos, $endforeach_pos - $foreach_pos + $endforeach_len);
            $foreach_content = substr($all_foreach, $foreach_len, strlen($all_foreach) - $endforeach_len - $foreach_len);
            if ($type === 'a') {
                $result = '';
                foreach ($data[$array_key]['value'] as $value) {
                    $mid_data = [$foreach[2]=>['cache'=>true, 'value'=>$value]];
                    $result .= static::commpile($foreach_content, $mid_data, $language);
                }
                return $result;
            } elseif($type === 'b') {
                $result = '';
                foreach ($data[$array_key]['value'] as $key => $value) {
                    $mid_data = [$foreach[2]=>['cache'=>true,'value'=>$key],$foreach[3]=>['cache'=>true, 'value'=>$value]];
                    $result .= static::commpile($foreach_content, $mid_data, $language);
                }
                return $result;
            } else {
                throw new TemplateException('未知的foreach类型');
            }
        } else {
            switch ($type) {
                case 'a':
                    return str_replace($tag, '<?php foreach $'.$array_key.' as '.$foreach[2].': ?>',
                            str_replace($endforeach_content, '', $content)
                        );
                case 'b':
                    return str_replace($tag, '<?php foreach $'.$array_key.' as '.$foreach[2].'=>'.$foreach[3].' : ?>',
                            str_replace($endforeach_content, '', $content)
                        );
                default:
                    throw new TemplateException('未知类型');
            }
        }
    }
    private static function parIf(string $content, array $data) : string
    {

        if (0 !== preg_match(static::$pattern['endif']['pattern'], $content)){
            $content = preg_replace(static::$pattern['if_a']['pattern'], static::$pattern['if_a']['replace'], $content);
            $content = preg_replace(static::$pattern['if_b']['pattern'], static::$pattern['if_b']['replace'], $content);
            $content = preg_replace(static::$pattern['elseif_a']['pattern'], static::$pattern['elseif_a']['replace'], $content);
            $content = preg_replace(static::$pattern['elseif_b']['pattern'], static::$pattern['elseif_b']['replace'], $content);
            $content = preg_replace(static::$pattern['else']['pattern'], static::$pattern['else']['replace'], $content);
            $content = preg_replace(static::$pattern['endif']['pattern'], static::$pattern['endif']['replace'], $content);
        }
        return $content;
    }
}