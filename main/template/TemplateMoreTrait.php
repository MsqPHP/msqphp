<?php declare(strict_types = 1);
namespace msqphp\main\template;

use msqphp\base;

trait TemplateMoreTrait
{
    /**
     * @param   array   $content_arr  数据内容
     * @param   array   $data         变量内容
     * @param   array   $language     语言内容
     * @return  string
     */
    /**
     * 解析foreach
     * @example
     *  $arr = ['arr'=>['cache'=>true, 'value'=>[1, 2, 3, 4, 5, 6, 7, 8, 9, 0]]];
     *  <{foreach $arr as $k => $v}>
     *      <{$k}>
     *  <{endforeach}>
     *  ---->
     *  <?php foreach($arr as $k=>$v) : ?>
     *      <?php echo $k?>
     *  <?php endforeach;?>
     *  ---->  0123456789;
     * @example
     *  $arr = [
     *      'arr'=>[
     *          'cache'=>true,
     *          'value'=>[
     *              'a'=>['A','B','C'],
     *              'b'=>['D','E','F']
     *          ]
     *      ]
     *  ];
     *  <{foreach $arr as $key => $value}>
     *      <{$key}>:
     *      <{foreach $value as $v}>
     *          <{$v}>
     *      <{endforeach}>
     *  <{endforeach}>;
     *  ---->
     *  <?php foreach($arr as $key=>$value) : ?>
     *      <?php echo $key;?>
     *      <?php foreach($value as $v)?>
     *          <?php echo $v;?>
     *      <?php endforeach;?>
     *  <?php endforeach;?>
     *  ---->  a:ABCb:DEF
     */
    private static function parForeach(array & $content_arr, array $data, array $language) : string
    {
        // 深度
        $deep = 1;
        // foreach 头
        $begin = array_shift($content_arr);
        // 循环内容
        $cycle_content = '';
        // 结果
        $result = '';
        // 左限定符
        $left_delimiter = static::$left_delimiter;

        // 获取foreach循环内容
        while (isset($content_arr[0])) {
            // 包括一个foreach循环
            if (base\str\Str::startsWith($content_arr[0],$left_delimiter.'foreach')) {
                ++$deep;
            }
            // 跳出一个foreach循环
            if (base\str\Str::startsWith($content_arr[0],[$left_delimiter.'endforeach', $left_delimiter.'/endforeach'])) {
                --$deep;
            }
            // 深度为0,则当前foreach闭合,去除foreach闭合标签,返回
            if ($deep === 0) {
                array_shift($content_arr);
                break;
            }
            // 将内容添加值foreach中间,即循环内容中
            $cycle_content .= array_shift($content_arr);
        }

        // 如果深度不为0,即未闭合,异常
        0 === $deep || static::exception('未闭合的foreach标签');

        // 获取foreach类型       foreach $array as $value || foreach $array as $key => $value
        if (0 !== preg_match(static::$pattern['foreach_a'], $begin, $foreach)) {
            $type = 'simple';
            $array_value = $foreach[2];
        } elseif(0 !== preg_match(static::$pattern['foreach_b'], $begin, $foreach)) {
            $type = 'complete';
            $array_key   = $foreach[2];
            $array_value = $foreach[3];
        } else {
            static::exception('错误的foreach语法');
        }
        // 数组名
        $array_name = $foreach[1];

        // 如果foreach所遍历的数组缓存
        if (isset($data[$array_name]) && $data[$array_name]['cache']) {
            // 数组循环
            foreach ($data[$array_name]['value'] as $key => $value) {
                // 编译,数据添加对应值,并将循环结果添加至结果中
                $result .= static::commpile($cycle_content, array_merge($data,
                    $type === 'simple'
                    ? [ $array_value =>['cache'=>true, 'value'=>$value] ]
                    : [ $array_key =>['cache'=>true, 'value'=>$key], $array_value=>['cache'=>true, 'value'=>$value] ]
                ), $language);
            }
        // 拼接并直接返回
        } else {
            // 开头
            $result .= $type === 'simple' ? '<?php foreach $'.$array_name.' as '.$array_value.': ?>' : '<?php foreach $'.$data_key.' as '.$array_key.'=>'.$array_value.' : ?>';
            // 循环内容编译
            $result .= static::commpile($cycle_content, $data, $language);
            // 结尾
            $result .= '<?php endforeach;?>';
        }
        return $result;
    }


    private static function parIf(array & $content_arr, array $data, array $language) : string
    {
        // 深度
        $deep      = 1;
        // 分支
        $branch    = 0;
        // if数据数组
        $if = [];
        // if0,即的一个if语句
        $if[0] = ['tag' => array_shift($content_arr), 'content' => '',];
        // 左定界符
        $left_delimiter = static::$left_delimiter;
        // 当有内容时
        while (isset($content_arr[0])) {
            // 包括一个if循环
            if (base\str\Str::startsWith($content_arr[0],$left_delimiter.'if')) {
                ++$deep;
            }
            // 跳出一个if循环
            if (base\str\Str::startsWith($content_arr[0],[$left_delimiter.'endif', $left_delimiter.'/endif'])) {
                --$deep;
            }

            // 深度为0,此时整个if语句段结束
            if (0 === $deep) {
                // 移除最后的endif
                array_shift($content_arr);
                // 跳出
                break;
            }

            // 如果深度为1,且以else开头,则为另一个分支,即新的elseif段或者else段
            if (1 === $deep && base\str\Str::startsWith($content_arr[0],$left_delimiter.'else')) {
                ++$branch;
                // 赋值
                $if[$branch] = [
                    'tag'     => array_shift($content_arr),
                    'content' => '',
                ];
            // 添加至内容中
            } else {
                $if[$branch]['content'] .= array_shift($content_arr);
            }
        }

        // 深度不为0
        0 === $deep || static::exception('未闭合的if语句');

        // 结果数组
        $result_if = [];

        while (isset($if[0])) {
            // if_a形式或者elseif_a形式
            if (0 !== preg_match(static::$pattern['if_a'], $if[0]['tag'], $if_match) || 0 !== preg_match(static::$pattern['elseif_a'], $if[0]['tag'], $if_match)) {
                // 变量a
                $var_a = $if_match[1];
                // 比较符
                $compare = $if_match[2];
                // 变量符
                $var_b = $if_match[3];
                // 如果缓存,替换为对应值
                $var_a = isset($data[$var_a]) && $data[$var_a]['cache'] ? $data[$var_a]['value'] : '$' . $var_a;
                $var_b = isset($data[$var_b]) && $data[$var_b]['cache'] ? $data[$var_b]['value'] : '$' . $var_b;

                // 结果是否缓存(即判断两个变量是否都缓存)
                if (isset($data[$var_a]) && $data[$var_a]['cache'] && isset($data[$var_b]) && $data[$var_b]['cache']) {
                    // 比较值为真
                    if (static::compare($data[$var_a]['value'], $data[$var_b]['value'], $compare)) {
                        // 直接结束,如果结果为空,直接替换为对应数据,否则以else结尾
                        $result_if[] = empty($result_if) ? static::commpile($if[0]['content'], $data, $language) : '<?php else: ?>' . static::commpile($if[0]['content'], $data, $language) . '<?php endif;?>';
                        // 跳出
                        break;
                    }
                } else {
                    // 添加一个if|elseif语句段
                    $result_if[] = '<?php ' . (empty($result_if) ? 'if' : 'elseif') . '('.static::phpValueTotext($var_a).$compare.static::phpValueTotext($var_b).') : ?>' . static::commpile($if[0]['content'], $data, $language);
                }
            // if_b形式
            } elseif (0 !== preg_match(static::$pattern['if_b'], $if[0]['tag'], $if_match) || 0 !== preg_match(static::$pattern['elseif_b'], $if[0]['tag'], $if_match)) {
                // 变量a
                $var_a = $if_match[1];
                // 比较符
                $compare = $if_match[2];
                // 变量符
                $var_b = static::textToPhpValue($if_match[3]);
                // 是否缓存
                if (isset($data[$var_a]) && $data[$var_a]['cache']) {
                    // 比较值为真
                    if (static::compare($data[$var_a]['value'], $var_b, $compare)) {
                        // 直接结束,如果结果为空,直接替换为对应数据,否则以else结尾
                        $result_if[] = empty($result_if) ? static::commpile($if[0]['content'], $data, $language) : '<?php else: ?>' . static::commpile($if[0]['content'], $data, $language) . '<?php endif;?>';
                        break;
                    }
                } else {
                    $result_if[] = '<?php ' . (empty($result_if) ? 'if' : 'elseif') . '($'.$var_a.$compare.static::phpValueTotext($var_b).') : ?>' . static::commpile($if[0]['content'], $data, $language);
                }
            // else形式
            } elseif (0 !== preg_match(static::$pattern['else'])) {
                $result_if[] = empty($result_if) ? static::commpile($if[0]['content'], $data, $language) : '<?php else: ?>' . static::commpile($if[0]['content'], $data, $language) . '<?php endif;?>';
            } else {
                static::exception('错误的if语句');
            }

            array_shift($if);

            // 如果执行到最后,则添加一个endif结尾
            empty($if) && $result_if[] = '<?php endif;?>';
        }
        return implode('', $result_if);
    }
}