msqphp beta 1.2
==========================

### ***msqphp is a simple but perfect framework.***

* * * * *


## 介绍

php7.1 轻量级框架

**php7.1beta版本出来了**
**有几个新特性不错**
**近期内会将框架代码微调**
**并不会向下兼容**
**如果使用出现异常请升级到7.1版本**
**php7.1更新到beta版了,相比7.0特性多了不少,最新的还支持异步??,感觉框架代码还得微调,预计月底前可以更新除数据库操作以外的较完整稳定版本**
暂时为beta版本

开源协议什么的不懂,但开源,免费,随意拆,随意使用

**除版权外不负责任何责任**

**未经实际项目测试,请慎重使用**

**未经实际项目测试,请慎重使用**

**未经实际项目测试,请慎重使用**

**框架功能收集中**

**框架代码改进中**

有位发邮件的朋友，抱歉，邮箱最近没有登录，今天登录才看到

由于php7.1版本更新，以及感觉到路由可以实现真路由（部分不再经过apache重写），以及很多地方的细节优化，所以短时间内可能没有更新
近段时间在不断微调框架，同时也在用框架做自己的一个项目，发现了一些bug,别优化别调整,所以进展可能较慢
但至少到目前为止，框架还是会持续更新的
下次更新预计就是一个稳定版本了，敬请期待。

0.0,这次不保证下次更新时间了,不能确定.


+ git : https://github.com/MsqPHP/msqphp
+ QQ交流群 : 566103528
+ 开发手册 : http://www.kancloud.cn/msqphp/msqphp

**开源,修改使用手册一个多月了,一句评价都没有.........**

## 理念

* * * * *

+ 简单,并到极致

+ 多而不乱,逻辑清晰

+ 灵活,小巧,不束缚

+ 告知式编程(目标全部(部分实现))
    > $cache->init()->key('cache')->value('test')->set();
    > 告诉框架|程序,你想要干什么,实现什么的与你无关

+ 易于扩展



##特性
+ php 7.0版本以下的各种特性
    >命名空间

    >闭包函数

    >traits等

+ composer支持
    >(虽然是废话)但这真的是一个很大的特性

    >并感谢composer

    >发现了一个新的框架运行速度比较方式

    >用时等同与几个composer自动加载类

    >完全为开发环境为4到5个

    >生产环境大概为  2到3个


+ aiload 智能加载 加强版 按需加载??惰性加载,不知道

    >**注:由于用composer加载类,所以需要需改composer类代码**

+ 灵活,强大,简单,逻辑清晰的路由类
    > 自己去看route相关介绍

+ 简单,强悍的视图类配套模版类
    > 视图三层缓存

    > + **固定值替换**
        + 零件->模块　(模版引擎解析,固定值或缓存值替换)

    > + **模块拼接**
        + 小模块->大模块->成品   (积木拼接)

    > + **真静态**
        + 成品->商品(直接展示,路由重写都不经过)

    > 像搭积木一样从零件开始拼接模块,展示模块

    > 视图多语,多主题,真静态,伪静态,中间件缓存等简易实现

+ 最容易的扩展方式

    >动态添加类函数,获取类属性

    >万能get, call, staticcall, 便于扩展且不影响效率

+ 基础类,常用基础函数集全(没有也能轻松扩展)

+ 框架核心类 核心实现,大多命令式编程,易读易用

+ 命令行支持(待完善)

+ 定时任务支持(伪成品)

+ 队列支持(猜想中)

+ 简易测试类

    > (伪成品)(不支持html页面测试,无法解决链式操作测试繁杂)

##**安装需求**

+ php 7.0及以上
+ mbstring ^_^ 我不会像larvel一样自己写一个强大的mb_string函数库什么的
+ pdo 否则数据库什么的就别想了
+ apache rewrite重写

##**框架获得**

+ **composer**

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

+ **git**
    + 下载git压缩包
    + 解压放置到web/msqphp目录下


##**安装**

框架路径:

**composer则框架位置在web/vendor/msqphp/framweok/**

**否则推荐放于web/msqphp/**

> 如果非composer安装,需要人工修改入口文件中框架路径


+ 配置入口目录目录下public,在入口目录下写入以下代码:

    ~~~

    <?php
    $root = dirname(__DIR__);

    //引入框架下的Framework.php

    require 框架路径'/Framework.php';

    \msqphp\Framework::install($root);

    ~~~

    > 明确表示权限相关是个坑,web目录需要写+读权限??

    > 如果无法安装

    > 可以人工复制框架路径/resource/install/下文件到web目录

+ 保存并运行

##**composer加载类是否使用**

如果不想使用composer类,只使用框架本身什么的需要修改
bootstrap/app.php
将常量定义为false或者删除
并将引用加载composer自动加载类注释
~~~
define('COMPOSER_AUTOLOAD', false);

//composer自动加载类

// require $root.'vendor/autoload.php';

~~~
+ 再次刷新即可


##目录结构(config中大多可配,改不改是你的事情)


~~~
www  WEB部署目录（或者子目录）
├──application           应用目录(顶级命名空间 app)
│  └──route.php          自定义的路由实现
│
├──applicationtests       应用测试目录(暂时无用)
│  └──test.php            测试逻辑文件
│
├──bootstrap              引导程序目录
│  └──app.php             引导程序文件(定义APP_DEBUG, 加载自动加载类, 加载框架并运行框架, 一个自定义的入口文件)
│
├──config               配置目录
│  └──config名字.php     对应的config
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
├──storage             仓库, 放东西的, 需要对应权限
│  ├──cache             文件缓存目录
│  ├──session           session缓存目录(如果文件的话)
│  ├──log               日志目录(暂未实现)
│  ├──framework         框架缓存目录, 一般会有config缓存,cron缓存,智能加载缓存
│  └──templates         模版目录
│     ├──cache          中间件缓存
│     └──last           最终缓存 非静态html 纯php代码+html代码并且经过压缩(也就是没有注释换行什么的文件缓存)
│
│──vendor               composer扩展文件夹(加载什么是你的事)
│  └──msqphp            msqphp框架
│
├──composer.json         composer定义文件
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


......详情请阅读开发手册 : http://www.kancloud.cn/msqphp/msqphp
