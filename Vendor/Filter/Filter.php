<?php declare(strict_types = 1);
namespace Core\Vendor\Filter;

class Filter
{
    /**
     * html过滤,输出纯html文本
     * @param  string|array $value 值
     * @return string|array        过滤后的值
     */
    static public function html($value) {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::html($v);
            }
            return $value;
        } elseif(is_string($value)) {
            return function_exists('htmlspecialchars') ? 
                htmlspecialchars($value,ENT_QUOTES) : 
                str_replace(array('&', '\'', '"', '<', '>'), array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), $value);
        } else {
                return $value;
        }
    }
    /**
     * 转义
     * @param  string|array $value 值
     * @return string|array        过滤后的值
     */
    static public function slashes($value) {
        if(is_array($value)) {
            foreach ($value as $k => $v) {
                $value[$k] = self::slashes($v);
            }
            return $value;
        } elseif(is_string($value)) {
                return addslashes($value);
        } else {
                return $value;
        }
    }
    /**
     * sql特殊字符过滤
     * @param  string $value 值
     * @return string        过滤后的值
     */
    static public function sql(string $str) : string {
        throw new Exception('未实现', 1);
        $sql = array('select','insert','update','delete','union','into','load_file','outfile','\'','\/\*','\.\.\/','\.\/');
        return str_replace($sql,'',$str);
    }
    static public function string(string $str) : string {
        throw new Exception('未知', 1);
        $value = str_replace(array("\0","%00","\r"), '', $value);
        $value = preg_replace(array('/[\\x00-\\x08\\x0B\\x0C\\x0E-\\x1F]/','/&(?!(#[0-9]+|[a-z]+);)/is'), array('', '&amp;'), $value);
        $value = str_replace(array("%3C",'<'), '&lt;', $value);
        $value = str_replace(array("%3E",'>'), '&gt;', $value);
        $value = str_replace(array('"',"'","\t",'  '), array('&quot;','&#39;','    ','&nbsp;&nbsp;'), $value);
    }
}