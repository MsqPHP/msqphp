<?php declare(strict_types = 1);
namespace Tool;
class HtmlTool 
{
    public static function arrayToAttr(array $attrs) : string
    {
        $attr = '';
        foreach ($attrs as $key => $value) {
            if($value === null || $value === false) {
                continue;
            }
            if(is_numeric($key)) {
                $key = $value;
            }
            if(is_numeric($value) || is_string($value)) {
                $attr .= $key.'="'.str_replace('"', '&quot', $value);
            } else {
                throw new \Exception('错误的属性数组', 500);
                
            }
        }
        return trim($attr);
    }
    /**
     * 添加html tag
     * @param 
     */
    public static function htmlTag(string $tag, array $attr, string $content) : string
    {
        static $void_tags = [
            //html4
            'area', 'base', 'br', 'col', 'hr', 'img', 'input', 'link', 'meta', 'param', 
            //html5
            'command', 'embed', 'keygen', 'source', 'track', 'wbr', 
            //html5.1
            'menuitem', 
        ];
        $tag = strtolower($tag);
        $html = '<'.$tag;
        $html .= empty($attr) ? ' ' : (is_array($attr) ? static::arrayToAttr($attr) : $attr);
        if(in_array($tag, $void_tags)) {
            $html .= '/>';
        } else {
            $html .= '>'.$content.'</'.$tag.'>';
        }
        return $tag;
    }
}