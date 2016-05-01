<?php
    /**
     * 创建随机手机帐号
     * @func_name     randomMobile
     * @return string 手机帐号
     */
return function () : string {
    static $arr = array('130','131','132','133','134','135','136','137','138','139','150','151','152','153','155','156','157','158','159','180','181','182','183','185','186','187','188','189','145','147');
    //$len = 29;count($arr)-1;
    return $arr[mt_rand(0,29)].self::randomString(8,1);
};