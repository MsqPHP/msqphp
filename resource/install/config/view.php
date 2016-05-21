<?php declare(strict_types = 1);
return [

    //是否允许主题设置
    'theme'                 =>  true,
    'theme_sport_list'      =>  ['default'],
    'default_theme'         =>  'default',
    //多语支持(配合route的Route::addLanguage(),配合使用)
    //或者单独设置language属性值
    'language'              => true,
    'default_language'      => 'zh-cn',
    'language_path'         => __DIR__.'/../resources/language/',
    // 默认的模版文件夹名称
    'tpl_ext'               =>  '.tpl',
    'tpl_path'        =>  __DIR__.'/../resources/templates/',
    'tpl_cache_ext'             =>  '.tplc',
    'tpl_cache_path'  =>  __DIR__.'/../storage/templates/cache/',
    'tpl_last_ext'             =>  '.tpll',
    'tpl_last_path'   =>  __DIR__.'/../storage/templates/last/',

    // 是否启用布局(默认加载位置为每个模块下,不会自动编译)
    'layout'             =>  true,
    // 布局文件名称 默认路径为当前主题下,主题不存在则模版路径下,支持多
    'layout_begin'     =>  'layout_begin.tpl',
    'layout_end'       =>  'layout_end.tpl',
];