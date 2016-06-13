<?php declare(strict_types = 1);
namespace msqphp\core\template;

use msqphp\base;

trait TemplateMoreTrait
{
    private static function parForeach(array & $content_arr, array $data, array $language) : string
    {
        $deep = 1;

        $begin = $content_arr[0];
        array_shift($content_arr);
        $middle = '';
        $result = '';
        $left_delim = static::$left_delim;

        while (isset($content_arr[0])) {
            if (base\str\Str::startsWith($content_arr[0],$left_delim.'foreach')) {
                ++$deep;
            }
            if (base\str\Str::startsWith($content_arr[0],[$left_delim.'endforeach', $left_delim.'/endforeach'])) {
                --$deep;
            }
            if ($deep === 0) {
                array_shift($content_arr);
                break;
            } else {
                $middle .= $content_arr[0];
                array_shift($content_arr);
            }
        }

        if (0 !== $deep) {
            throw new TemplateException('未闭合的foreach标签');
        }

        if (0 !== preg_match(static::$pattern['foreach_a'], $begin, $foreach)) {
            $type = 'a';
        } elseif(0 !== preg_match(static::$pattern['foreach_b'], $begin, $foreach)) {
            $type = 'b';
        } else {
            throw new TemplateException('错误的foreach语法');
        }

        if (isset($data[$foreach[1]]) && $data[$foreach[1]]['cache']) {
            foreach ($data[$foreach[1]]['value'] as $key => $value) {
                $mid_data = array_merge($data,
                    $type === 'a'
                    ? [$foreach[2]=>['cache'=>true, 'value'=>$value]]
                    : [$foreach[2]=>['cache'=>true, 'value'=>$key], $foreach[3]=>['cache'=>true, 'value'=>$value]]
                );
                $result .= static::commpile($middle, $mid_data, $language);
            }
        } else {
            $result .= $type === 'a' ? '<?php foreach $'.$foreach[1].' as '.$foreach[2].': ?>' : '<?php foreach $'.$data_key.' as '.$foreach[2].'=>'.$foreach[3].' : ?>';
            $result .= static::commpile($middle, $data, $language);
            $result .= '<?php endforeach;?>';
        }
        return $result;
    }
    private static function parIf(array & $content_arr, array $data, array $language) : string
    {
        $deep      = 1;
        $branch    = 0;
        $if = [];
        $if[0]['tag'] = $content_arr[0];
        $if[0]['content'] = '';
        $if[0]['cached'] = false;
        $if[0]['mached'] = false;
        array_shift($content_arr);
        $left_delim = static::$left_delim;

        while (isset($content_arr[0])) {
            if (base\str\Str::startsWith($content_arr[0],$left_delim.'if')) {
                ++$deep;
            }
            if (base\str\Str::startsWith($content_arr[0],[$left_delim.'endif', $left_delim.'/endif'])) {
                --$deep;
            }
            if (0 === $deep) {
                array_shift($content_arr);
                break;
            } else {
                if (1 === $deep && base\str\Str::startsWith($content_arr[0],$left_delim.'else')) {
                    ++$branch;
                    $if[$branch]['tag'] = $content_arr[0];
                    $if[$branch]['content'] = '';
                    array_shift($content_arr);
                    continue;
                } else {
                    $if[$branch]['content'] .= $content_arr[0];
                    array_shift($content_arr);
                }
            }
        }

        if (0 !== $deep) {
            throw new TemplateException('未闭合的if语句');
        }

        if (0 !== preg_match(static::$pattern['if_a'], $if[0]['tag'], $begin_match)) {
            $var_a = $begin_match[1];
            $compare = $begin_match[2];
            $var_b = $begin_match[3];
            if (isset($data[$var_a]) && $data[$var_a]['cache'] && isset($data[$var_b]) && $data[$var_b]['cache']) {
                if (static::compare($data[$var_a]['value'], $data[$var_b]['value'], $compare)) {
                    return static::commpile($if[0]['content'], $data, $language);
                } else {
                    $if[0]['cached'] = true;
                }
            } elseif (isset($data[$var_a]) && $data[$var_a]['cache']) {
                $if[0]['result'] = '<?php if('.var_export($data[$var_a]['value'], true).$compare.'$'.$var_b.') : ?>' . static::commpile($if[0]['content'], $data, $language);
            } elseif (isset($data[$var_b]) && $data[$var_b]['cache']) {
                $if[0]['result'] = '<?php if($'.$var_a.$compare.var_export($data[$var_b]['value'], true).') : ?>' . static::commpile($if[0]['content'], $data, $language);
            } else {
                $if[0]['result'] = '<?php if($'.$var_a.$compare.'$'.$var_b.') : ?>' . static::commpile($if[0]['content'], $data, $language);
            }
        } elseif (0 !== preg_match(static::$pattern['if_b'], $if[0]['tag'], $begin_match)) {
            $var_a = $begin_match[1];
            $compare = $begin_match[2];
            $var_b = static::toValue($begin_match[3]);
            if (isset($data[$var_a]) && $data[$var_a]['cache']) {
                if (static::compare($data[$var_a]['value'], $var_b, $compare)) {
                    return static::commpile($if[0]['content'], $data, $language);
                } else {
                    $if[0]['cached'] = true;
                }
            } else {
                $if[0]['result'] = '<?php if($'.$var_a.$compare.$var_b.') : ?>' . static::commpile($if[0]['content'], $data, $language);
            }
        } else {
            throw new TemplateException('错误的if语句');
        }

        for ($i = 1, $l = count($branch); $i < $l; ++$i) {
            $if[$branch]['cached'] = false;
            $if[$branch]['mached'] = false;
            if (0 !== preg_match(static::$pattern['if_a'], $if[$branch]['tag'], $begin_match)) {
                $var_a = $begin_match[1];
                $compare = $begin_match[2];
                $var_b = $begin_match[3];
                if (isset($data[$var_a]) && $data[$var_a]['cache'] && isset($data[$var_b]) && $data[$var_b]['cache']) {
                    if (static::compare($data[$var_a]['value'], $data[$var_b]['value'], $compare)) {
                        $if[$branch]['result'] = static::commpile($if[$branch]['content'], $data, $language);
                        $if[$branch]['mached'] = true;
                        break;
                    } else {
                        $if[$branch]['cached'] = true;
                    }
                } elseif (isset($data[$var_a]) && $data[$var_a]['cache']) {
                    $if[$branch]['result'] = '<?php elseif('.var_export($data[$var_a]['value'], true).$compare.'$'.$var_b.') : ?>' . static::commpile($if[$branch]['content'], $data, $language);
                } elseif (isset($data[$var_b]) && $data[$var_b]['cache']) {
                    $if[$branch]['result'] = '<?php elseif($'.$var_a.$compare.var_export($data[$var_b]['value'], true).') : ?>' . static::commpile($if[$branch]['content'], $data, $language);
                } else {
                    $if[$branch]['result'] = '<?php elseif($'.$var_a.$compare.'$'.$var_b.') : ?>' . static::commpile($if[$branch]['content'], $data, $language);
                }
            } elseif (0 !== preg_match(static::$pattern['if_b'], $if[$branch]['tag'], $begin_match)) {
                $var_a = $begin_match[1];
                $compare = $begin_match[2];
                $var_b = static::toValue($begin_match[3]);
                if (isset($data[$var_a]) && $data[$var_a]['cache']) {
                    if (static::compare($data[$var_a]['value'], $var_b, $compare)) {
                        $if[$branch]['result'] = static::commpile($if[$branch]['content'], $data, $language);
                        $if[$branch]['mached'] = true;
                        break;
                    } else {
                        $if[$branch]['cached'] = true;
                    }
                } else {
                    $if[$branch]['result'] = '<?php elseif($'.$var_a.$compare.$var_b.') : ?>' . static::commpile($if[$branch]['content'], $data, $language);
                }
            } elseif (0 !== preg_match(static::$pattern['endif'], $if[$branch]['tag'], $begin_match)) {
                if ($i = $branch - 1) {
                    $if[$branch]['result'] = static::commpile($if[$branch]['content'], $data, $language);
                } else {
                    throw new TemplateException('错误的if语句');
                }
            } else {
                throw new TemplateException('错误的if语句');
            }
        }

        $cache = true;
        $mached = true;
        foreach ($if as $value) {
            $cache = $value['cache'] && $cache;
            if ($cache) {
                if ($value['mached']) {
                    return $value['content'];
                }
            } else {
                if ($value['mached']) {
                    $result .= $value['result'];
                    break;
                } else {
                    $result .= $value['result'];
                }
            }
        }


        return $result;
    }
}
