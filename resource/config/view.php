<?php declare(strict_types = 1);
defined('APP_DEBUG') || die('不合理访问');
return [
    //是否允许多主题
    'multi_theme'           =>  true,
    'theme_sport_list'      =>  ['default'],
    // 默认模板主题名称 
    'default_theme'         =>  'default',
    // 模板引擎普通标签开始标记
    'TMPL_L_DELIM'          =>  '<{',
           // 模板引擎普通标签结束标记
    'TMPL_R_DELIM'          =>  '}>',
    
    // 默认模板后缀名
    'tpl_ext'               =>  '.tpl',  
    // 模版文件缓存前缀
    'tpl_c_pre'             =>  'cache_',
    //模版文件缓存后缀
    'tpl_c_ext'             =>  '.tplc', 
    // 模版文件缓存前缀
    'tpl_l_pre'             =>  'last_', 
    //模版文件缓存后缀
    'tpl_l_ext'             =>  '.tpll', 
    // 默认的模版文件夹名称
    'templates_path'        =>  __DIR__.'/../resources/templates/',
    'language_path'         =>  __DIR__.'/../resources/language/',
    // 默认的模版缓存文件夹名称
    'templates_cache_path'  =>  __DIR__.'/../storage/templates/cache/',
    // 默认的模版缓存文件夹名称
    'templates_last_path'   =>  __DIR__.'/../storage/templates/last/',
    // 是否启用布局(默认加载位置为每个模块下,不会自动编译)
    'layout_on'             =>  true,
    // 当前布局名称 默认为layout
    'latout_begin_file'     =>  'layout_begin.tpl',
    // 当前布局名称 默认为layout
    'latout_end_file'       =>  'layout_end.tpl',

];