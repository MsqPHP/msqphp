msqphp beta 1.1
==========================

### ***msqphp is a simple but perfect framework.***

* * * * *


##使用须知

开源,免费,无需求,函数随意改,代码随意拆

**未经实际项目测试,请慎重使用**

**未经实际项目测试,请慎重使用**

**未经实际项目测试,请慎重使用**

**框架功能收集中**

**框架代码改进中**

**框架为测试版本,除版权外不负责任何责任**

**TAT,写代码去了,成品出来再说**

+ git : https://github.com/MsqPHP/msqphp
+ 开发手册 : http://www.kancloud.cn/msqphp/msqphp
+ QQ交流群 : 566103528

## 理念

* * * * *
+ 多而不乱
+ 简单,并到极致
+ 告知式编程(目标全部(部分实现))
+ 易于扩展
+ 灵活,不束缚


##特性
+ php 7.0,命名空间, 闭包函数,traits等

+ 最灵活的route类,没有之一(所有逻辑都交给你,你说灵不灵活)

+ 较强大的route类,mvc,无限模块,多语支持,ip,域名限制等等

+ composer支持(虽然是废话)(但这真的是一个很大的特性)

+ 最容易的扩展方式,万能get, call, staticcall, 便于扩展且不影响效率

+ 伪面向对象, 原因如上

+ 基础类,常用基础函数集全(没有也能轻松扩展)

+ 框架核心类 核心实现,大多命令式编程,易读易用

+ 扩展类 待完善

+ 自己配套的模版引擎及视图类,实现三层缓存(1.固定值替换;2.模块拼接;3.真静态)

+ 视图多语,多主题,真静态,伪静态,中间件缓存等简易实现

+ 智能加载(伪成品,需要且必须修改composer一行代码)

+ 命令行支持(待完善)

+ 定时任务支持(未成品)

+ 队列支持(猜想中)

+ 简易测试类(伪成品)(不支持html页面测试,无法解决链式操作测试繁杂)

##**安装需求**
+ composer 安装 更新
+ composer 修改代码ClassLoader.php中410行includeFile函数为:

    ~~~

    function includeFile($file)
    {
        $GLOBALS['autoloader_class'][] = $file;
        include $file;
    }
    ~~~
    > 不影响框架正常使用,但可以实现智能加载,所以推荐每次更新添加代码(如果没错的话,你大概常年不会composer更新的);
+ php 7.0及以上
+ mbstring ^_^ 我不会像larvel一样自己写一个强大的mb_string函数库什么的
+ pdo 否则数据库什么的就别想了
+ apache rewrite重写

##安装

**windows下好像不行,请人工复制vendor/msqphp/framework/resource/install/下文件到web目录**

+ composer.json:
~~~
{
    "require": {
        "php": ">=7.0.0",
        "msqphp/framework": "1.0.*"
    },
    "autoload": {
        "psr-4": {
            "app\\": "application/",
        }
    },
    "minimum-stability": "dev"
}
~~~
+ composer update
+ 配置入口目录目录下public,在入口目录下写入以下代码:
~~~
<?php
$root = dirname(__DIR__);
require $root.'vendor/msqphp/framework/Framework.php';
\msqphp\Framework::install($root);
~~~
+ 保存并运行
+ 再次刷新即可


##目录结构(config中大多可配,改不改是你的事情)


~~~
www  WEB部署目录（或者子目录）
├──application           应用目录(顶级命名空间 App)
│  └──route.php          自定义的路由实现
│
├──applicationtests     应用测试目录(暂时无用)
│  └──test.php          测试逻辑文件
│
├──bootstrap            引导程序目录
│  └──app.php           app文件(定义APP_DEBUG, 加载自动加载类, 加载框架并运行框架, 一个自定义的入口文件)
│
├──config               配置目录
│  └──config名字.php    对应的config
│
│
├──library              图书馆, 也就是你自己的类文件函数什么的, 怎么加载是你的事
│  └──msqphp
|     └──framework      msqphp框架扩展目录, 万能get, call, callStatic扩展(如果在这里面放类什么的, 将composer autoload中添加psr4:msqphp:当前路径[暂定])
│
├──public                WEB目录（对外访问目录）
│  ├──server.php         apache入口文件 , 载入引导程序
│  └──.htaccess          用于apache的重写
|
├──resources            资源目录
│  ├──language          应用类语言目录  语言/[路由分组/]对应视图.php
│  ├──templates         应用类视图目录  [主题/][路由分组/][语言/]对应视图.php (允许单一视图配置对应语言的视图文件)
│  └──views             基础视图目录目录 error.html 什么的
│
│
├──storage             仓库, 放东西的, 需要对应权限
│  ├──cache             文件缓存目录
│  ├──session           session缓存目录(如果文件的话)
│  ├──log               日志目录(暂未实现)
│  ├──framework         框架缓存目录, 一般会有config缓存,cron缓存,自动加载缓存
│  └──templates         模版目录
│     ├──cache          中间件缓存
│     └──last           最终缓存 非静态html 纯php代码+html代码并且经过压缩(也就是没有注释换行什么的文件缓存)
│
│──vendor               composer扩展文件夹(加载什么是你的事)
│  └──msqphp            msqphp框架
│
├──composer.json         composer定义文件
~~~

##**应用目录结构**
###*多模块mavc(a可取)*

* * * * *
~~~
├──application           应用目录
│  ├──route.php          路由流程类
│  ├──home
│  │  ├──controller      逻辑类         主逻辑
│  │  ├──action          数据处理加工   只加工, 不读取
│  │  ├──model           数据读取       读和取, 不加工
│  │  ├──view            视图展示       只展示 或者直接从Action,Model获得展示数据(仅推荐不需要任何参数, 全局一致(语言除外), 并进行html缓存的数据) (推荐视图局部缓存时使用)
│  │  └....
│  └....
//route配置

Route::addGroup([
    'name'      =>'module',
    'allowed'   =>['home','back','luntan'],
    'default'   =>'home',
    'namespace' =>true,
]);

Route::group('module', 'home', function () {
    //controller模块
    Route::addGroup([
        'name'      => 'controller',
        'allowed'   => ['index','user'],
        'default'   => 'index',
        'namespace' => 'controller',
    ]);

    Route::group('controller', 'index', function () {
        //Route::get('login', 'Index@login');
        ...Route::get();
        ...Route::post();
        ...Route::ajax();
    });
});
~~~

###*单模块mavc:*

* * * * *
~~~
├──application           应用目录
│  ├──route.php          路由流程类
│  ├──controller      逻辑类         主逻辑
│  ├──action          数据处理加工   只加工, 不读取
│  ├──model           数据读取       读和取, 不加工
│  ├──view            视图展示       只展示 或者直接从Action,Model获得展示数据(仅推荐不需要任何参数, 全局一致(语言除外), 并进行html缓存的数据) (推荐视图局部缓存时使用)
│  └....

//route配置

Route::addGroup([
    'name'      => 'controller',
    'allowed'   => ['index','user'],
    'default'   => 'index',
    'namespace' => 'controller',
]);
Route::group('controller', 'index', function () {
    //Route::get('login', 'Index@login');
    ...Route::get();
    ...Route::post();
    ...Route::ajax();
});
~~~


* * * * *

###*多模块自定以(我现在使用,简单暴力,无限制什么的):*

> 可以直接调用视图类方法,模型方法什么的,扩展加文件,逻辑也清晰,没那么多限制

> 原理,分组时将命名空间匹配到对应目录:\\app\\home\\index\\

> 直接调用此时对应类对应方法

> 支持无限递归之类的

~~~

├──application           应用目录
│  ├──route.php          路由流程类
│  ├──home
│  │  ├──index
│  │  │  ├──Controller.php      逻辑类         主逻辑
│  │  │  ├──Action.php          数据处理加工   只加工, 不读取
│  │  │  ├──Model.php           数据读取       读和取, 不加工
│  │  │  ├──View.php            视图展示       只展示 或者直接从Action,Model获得展示数据(仅推荐不需要任何参数, 全局一致(语言除外), 并进行html缓存的数据) (推荐视图局部缓存时使用)
│  │  │   └....
│  │  └....
│  └....

//route配置

Route::addGroup([
    'name'      =>'module',
    'allowed'   =>['home','back','luntan'],
    'default'   =>'home',
    'namespace' =>true,
]);

Route::group('module', 'home', function () {
    //controller模块
    Route::addGroup([
        'name'      => 'controller',
        'allowed'   => ['index','user'],
        'default'   => 'index',
        'namespace' => true,
    ]);

    Route::group('controller', 'index', function () {
        //Route::get('login', 'View@login');
        ...Route::get();
        ...Route::post();
        ...Route::ajax();
    });
});

~~~

##**框架结构**

~~~

├──msqphp                msqphp框架
│  └──framework          框架
│    │
│    ├──base             基类(放置一些基础类,全部为静态类静态方法,trait万能静态call)
│    ├──core             核心类(放置一些核心类,以基类为基础,互相依赖,主要为框架实现)
│    ├──vendor           扩展类(提供一些不必要但可能使用的功能,例如图片处理,上传功能等等)
│    ├──traits           接口类??trait类集合,提供类基础构造组件,万能call,instance等等
│    │
│    ├──resource         基础视图资源,安装资源
│    │
│    ├──test             测试类及框架测试
│    │
│    ├──Environment.php  框架环境类 主运行逻辑负责
│    │
│    ├──App.php          应用方式模式运行
│    ├──Cli.php          命令行模式运行
│    ├──Test.php         测试入口文件
│    ├──Framework.php    框架安装,更新等框架本身操作
│    │
│    ├──composer.json    composer,不解释
│    ├──composer.lock    composer,不解释
│    └──README.md        git

~~~


......详情请阅读开发手册 : http://www.kancloud.cn/msqphp/msqphp