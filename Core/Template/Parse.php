<?php declare(strict_types = 1);
namespace Msqphp\Core\Template;

class Parse {
    private static $language = [];
    private static $preg = array(
        //匹配变量并生成一个组,内容为变量名
        'var'   =>  '\$([A-Za-z_][A-Z0-9a-z_]*)',
        //匹配至少一个空格
        'blank' =>  '\s+',
        //匹配可能有的空格
        'may_blank' => '\s*',
        //匹配比较运算符，并生成一个组，内容为对应的比较运算符   
        //<=,>=,<,>,<>,!=,==,===,!==
        'commpile'  => '(\<\=|\>=|\<|\>|\<\>|\!\=|\=\=|\=\=\=|\!\=\=)',
        //匹配值（数字或字符串）
        'value' => '([\'][^\']*[\']|[\"][^\"]*[\"]|[0-9])',
    );
    public static function commpile(string $content,array $vars = [],array $language = [],array $config = []) :string {
        $left  = preg_quote($config['left'] ?? '<{');
        $right = preg_quote($config['right'] ?? '}>');
        $left  = '/'.$left.'\\s*';
        $right = '\\s*'.$right.'\\s*/';

        //先包含文件
        $content = static::parInclude($content,$left,$right);
        //解析常量
        $content = static::parConstant($content,$left,$right);
        $content = static::parLanguae($content,$language,$left,$right);
        
        $content = static::all($content,$vars,$left,$right);
        //返回
        return $content;
    }
    private static function all(string $content,$vars,$left,$right) : string
    {
        $content = static::parForeach($content,$vars,$left,$right);
        $content = static::parIf($content,$vars,$left,$right);

        //解析剩余凌散变量
        $content = static::parVar($content,$vars,$left,$right);
        $content = static::parArray($content,$vars,$left,$right);
        return $content;
    }
    /**
     * 解析包含文件
     * @example <{include 'file.html'}>  =>    file_get_contents(file.html);
     * @param   string $content 内容
     * @param   string $left    正则开始
     * @param   string $right   正则结束
     * @return  string
     */
    private static function parInclude(string $content,string $left,string $right) : string 
    {
        return preg_replace_callback(
            $left.'include\s+([\w\/\.:-]+)'.$right,
            function($matches){
                $file = $matches[1];
                if (!is_file($file)) {
                    throw new ParseException($file.'文件不存在', 500);
                    return '';
                } else {
                    return file_get_contents($file);
                }
            },
            $content
        );
    }
    /**
     * 解析常量
     * @example <{constant.IMAGE}>  =>    http://image.test.com/ (一次解析,直接替换)
     * @param   string $content 内容
     * @param   string $left    正则开始
     * @param   string $right   正则结束
     * @return  string
     */
    private static function parConstant(string $content,string $left,string $right) : string
    {
        return preg_replace_callback(
            array(
                $left.'constant\.([A-Za-z_]+)'.$right,
                $left.'cont\.([A-Za-z_]+)'.$right,
            ),
            function($matches) {
                if (!defined($matches[1])) {
                    throw new ParseException($matches[1].'常量未定义');
                    return '';
                }
                return constant($matches[1]);
            },
            $content
        );
    }
    /**
     * 解析常量
     * @example <{constant.IMAGE}>  =>    http://image.test.com/ (一次解析,直接替换)
     * @param   string $content 内容
     * @param   array  $lanuage 语言
     * @param   string $left    正则开始
     * @param   string $right   正则结束
     * @return  string
     */
    private static function parLanguae(string $content,array $language,string $left,string $right) : string
    {
        return preg_replace_callback(
            array(
                $left.'language\.([A-Za-z_]+)'.$right,
                $left.'lang\.([A-Za-z_]+)'.$right,
            ),
            function($matches) use ($language) {
                if (!isset($language[$matches[1]])) {
                    throw new ParseException($matches[1].'对应语言不存在');
                    return '';
                }
                return $language[$matches[1]];
            },
            $content
        );
    }
    /**
     * 解析foreach
     * @example $array = array('hello','world');
     * @example <{foreach $array as $value}>   -->       <?php foreach($array as $value):?>
     *              <{$v}>                     -->       <?php echo $v;?>
     *          <{endforeach}>                 -->       <?php endforeach;?>
     * @example <{foreach $array as $value}>   -->       ''
     *              <{$v}>                     -->       
     *          <{endforeach}>                 -->       <?php endforeach;?>
     * 
     */
    private static function parForeach(string $content,array $vars,string $left,string $right) : string
    {
        //正则
        $foreach_end = '(\/endforeach|endforeach)';
        //形式A       foreach $array as $value
        $foreach_a = 'foreach\s+\$([A-Za-z_][A-Z0-9a-z_]*)\s+as\s+\$([A-Za-z_][A-Z0-9a-z_]*)';
        //形式B       foreach $array as $key => $value
        $foreach_b = 'foreach\s+\$([A-Za-z_][A-Z0-9a-z_]*)\s+as\s+\$([A-Za-z_][A-Z0-9a-z_]*)\s*\=\>\s*\$([A-Za-z_][A-Z0-9a-z_]*)';
        //当有endforeach时
        while(0 !== preg_match($left.$foreach_end.$right,$content,$matches_end)) {
            //抓取是否存在foreach 形式 A
            $bool_a = preg_match($left.$foreach_a.$right,$content,$matches_a);
            //抓取是否存在foreach 形式 B
            $bool_b = preg_match($left.$foreach_b.$right,$content,$matches_b);

            //获得对应形式,1=形式A,2=形式B
            if ($bool_a === 1 && $bool_b === 0) {
                $type = 1;
            } elseif ($bool_b === 1 && $bool_a === 0) {
                $type = 2;
            } elseif ($bool_a === 0 && $bool_b === 0) {
                throw new ParseException('未知的foreach类型', 1);
            } else {
                //都存在取最前面的一个
                $type = strpos($content,$matches_a[0]) > strpos($content,$matches_b[0]) ? 2 : 1;
            }

            $matches = $type === 1 ? $matches_a : $matches_b;

            //解析

            //变量存在并缓存
            if (isset($vars[$matches[1]]) && $vars[$matches[1]]['cache']) {
                /**
                 * .....code                      --之前
                 * <{foreach $array as $value}>   --start
                 *     .....code                  --中间
                 * --end       <{endforeach}>
                 * .....code                      --之后
                 */
                //开始位置
                $start = strpos($content,$matches[0])+strlen($matches[0]);
                //结束位置
                $end = strpos($content,$matches_end[0]);
                //之前字符串 
                $begin_str = str_replace($matches[0],'',substr($content,0,$start));
                //之后
                $after_str = str_replace($matches_end[0],'',substr($content,$end));
                //中间
                $mid_content = substr($content,$start,$end-$start);

                //中间值进行进一步处理
                $mid_str = '';
                if ($type === 1) {
                    foreach ($vars[$matches[1]]['value'] as $value) {
                        $data = array($matches[2]=>array('cache'=>true,'value'=>$value));
                        $mid_str .= self::all($mid_content,$data,$left,$right);
                    }
                } elseif ($type === 2) {
                    foreach ($vars[$matches[1]]['value'] as $key => $value) {
                        $data = array($matches[2]=>array('cache'=>true,'value'=>$key),$matches[3]=>array('cache'=>true,'value'=>$value));
                        $mid_str .= self::all($mid_content,$data,$left,$right);
                    }
                }
                //拼接
                $content = $begin_str.$mid_str.$after_str;
            } else {
            //直接返回
                $content = preg_replace($left.$foreach_end.$right,'<?php endforeach;?>',$content);                
                $content = $type === 1 ? preg_replace($left.$foreach_a.$right,'<?php foreach($\\1 as $\\2):?>',$content) : preg_replace($left.$foreach_b.$right,'<?php foreach($\\1 as $\\2=>$\\3):?>',$content);
            }
        }
        return $content;
    }

    /**
     * 解析变量
     * @example         <{$name}>      -->  <?php echo $name;?>
     * @example (cache) <{$name}>      -->  value
     */
    private static function parVar($content,$vars,$left,$right) : string
    {
        return preg_replace_callback(
            $left.'\$([A-Za-z_][A-Z0-9a-z_]*)'.$right,
            function($matches) use ($vars) {
                $key = $matches[1];
                if (isset($vars[$key]) && $vars[$key]['cache']) {
                    if (is_array($vars[$key]['value'])) {
                        throw new ParseException($key.'数组被当作普通变量使用');
                    }
                    return $vars[$key]['value'];
                } else {
                    return '<?php echo $'.$key.';?>';
                }
            }, 
            $content
        );
    }
    private static function parArray($content,$vars,$left,$right) : string
    {
        return preg_replace_callback(
            $left.'\$([\w]+)([\[\w\'\]]+)'.$right,
            function($matches) use ($vars) {
                $key = $matches[1];
                $val = $matches[2];
                if (isset($vars[$key]) && $vars[$key]['cache'] === true) {
                    $a = 'return $vars[\''.$key.'\'][\'value\']'.$val.';';
                    return eval($a);
                } else {
                    return '<?php echo $'.$key.$val.';?>';
                }
            },
            $content
        );
    }
    private static function parIf(string $content,array $vars,string $left,string $right) {
        
        $patten = array(
            /*
                <{if $a = 5}>               =>  if($a=5) :
                <{if $a>=5}>                =>  if($a>=5) :
                <{if $a>$b}>                =>  if($a>$b) :
                <{else}>                    =>  else :
                <{elseif $b = 10}>          =>  elseif($b=10) :
                <{endif}>                   =>  elseif($b=10) :
            */
            'if\s+\$([A-Za-z_][A-Z0-9a-z_]*)\s*(\<\=|\>=|\<|\>|\<\>|\!\=|\=\=|\=\=\=|\!\=\=)\s*\$([A-Za-z_][A-Z0-9a-z_]*)' => '<?php if(\$\\1\\2$\\3) : ?>',
            'if\s+\$([A-Za-z_][A-Z0-9a-z_]*)\s*(\<\=|\>=|\<|\>|\<\>|\!\=|\=\=|\=\=\=|\!\=\=)\s*([\'][^\']*[\']|[\"][^\"]*[\"]|[0-9])' => '<?php if(\$\\1\\2\\3) : ?>',
            'elseif\s+\$([A-Za-z_][A-Z0-9a-z_]*)\s*(\<\=|\>=|\<|\>|\<\>|\!\=|\=\=|\=\=\=|\!\=\=)\s*\$([A-Za-z_][A-Z0-9a-z_]*)' => '<?php elseif(\$\\1\\2$\\3) : ?>',
            'elseif\s+\$([A-Za-z_][A-Z0-9a-z_]*)\s*(\<\=|\>=|\<|\>|\<\>|\!\=|\=\=|\=\=\=|\!\=\=)\s*([\'][^\']*[\']|[\"][^\"]*[\"]|[0-9])' => '<?php elseif(\$\\1\\2\\3) : ?>',
            'else' => '<?php else : ?>',
            '(\/endif|endif)' => '<?php endif ;?>',
        );
        if (0 !== preg_match($left.'(\/endif|endif)'.$right, $content)){
            foreach ($patten as $key => $value) {
                $content = preg_replace($left.$key.$right,$value,$content);
            }
        }
        return $content;
    }
}