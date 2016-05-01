<?php declare(strict_types = 1);
namespace Msqphp\Base\File;
return function (string $content) :string {
    $content = preg_replace_callback('/<script([\s\S]*)>([\s\S]*)<\/script>/',function($matches){
        $result = '<script'.$matches[1].'>';
        $js = $matches[2];
        $pattern = array(
            '/\/\/([^\n\r]*)/','/\/\*([\s\S]*)\*\//','/^\s*/','/
/',
        );
        $js = preg_replace($pattern, '', $js);
        $js = preg_replace('/([\{\}\;])\s+/', '\\1', $js);
        $result .= $js . '</script>';
        return $result;
    },$content);
    //删除空格
    $content = preg_replace('/>\s*</','><',$content);
    //删除换行符
    $content = preg_replace('/
/','',$content);
    //删除注释
    $content = preg_replace('/<\!\-\-([\s\S]*)\-\->/','',$content);
    //js格式化
    return $content;
};