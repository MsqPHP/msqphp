msqphp beta 2.0
==========================

### ***msqphp is a simple but perfect framework.***

* * * * *


## 介绍

php7.1 轻量级框架

开源协议什么的不懂,但开源,免费,随意拆,随意使用

**除版权外不负责任何责任**

**未经实际项目测试,请慎重使用**

**未经实际项目测试,请慎重使用**

**未经实际项目测试,请慎重使用**

**框架功能收集中**

**框架代码改进中**

+ git : https:// github.com/MsqPHP/msqphp
+ QQ交流群 : 566103528
+ 开发手册 : http:// www.kancloud.cn/msqphp/msqphp

## 理念

* * * * *

+ 简单,并到极致

+ 多而不乱,逻辑清晰

+ 灵活,小巧,不束缚

+ 告知式编程(目标全部(部分实现))
    > $cache->init()->key('cache')->value('test')->set();
    > 告诉框架|程序,你想要干什么,实现什么的与你无关

+ 易于扩展

##**框架需求**

+ php 7.1及以上
+ mbstring 我不会像larvel一样自己写一个强大的mb_string函数库什么的
+ pdo 否则数据库什么的就别想了
+ apache rewrite重写

##特性
+ php 7.1版本以下的各种特性
    >命名空间

    >闭包函数

    >traits等

+ composer支持
    >(虽然是废话)但这真的是一个很大的特性

+ aiload 智能加载 加强版 按需加载??惰性加载,不知道

    >**注:由于用composer加载类,所以需要需改composer类代码**

+ 灵活,强大,简单,逻辑清晰的路由类
    > 自己去看route相关介绍

+ 简单,强悍的视图类配套模版类
    > 视图多语,多主题,真静态,伪静态,中间件缓存等简易实现

+ 最容易的扩展方式

    >动态添加类函数,获取类属性

    >万能get, call, staticcall, 便于扩展且不影响效率

+ 命令行支持(待完善)

+ 定时任务支持

+ 队列支持(猜想中)

+ 简易测试类(不支持html页面测试)

##**框架获取**

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
                    "test\\": "test/",
                    "msqphp\\": "library/msqphp/framework/"
                }
            },
            "minimum-stability": "dev"
        }
        ~~~
    + composer update

+ **git**
    + 下载git压缩包
    + 解压放置到web/msqphp目录下


##**框架安装**

框架路径:

**composer则框架位置在web/vendor/msqphp/framweok/**

**否则推荐放于web/msqphp/**

> 如果非composer安装,需要人工修改入口文件中框架路径


+ 配置入口目录目录下public,在入口目录下写入以下代码:

    ~~~

    <?php
    $root = dirname(__DIR__);

    // 引入框架下的Framework.php

    require 框架路径'/Framework.php';

    \msqphp\Framework::install($root);

    ~~~

    > 明确表示权限相关是个坑,web目录需要写+读权限??

    > 如果无法安装

    > 可以人工复制框架路径/resource/install/下文件到web目录

+ 保存并运行

##目录结构(config中大多可配,改不改是你的事情)

~~~
www  WEB部署目录（或者子目录）
├──application           应用目录(顶级命名空间 App)
│  └──route.php          自定义的路由实现
│
├──test                 应用测试目录
│  └──test.php          测试逻辑文件
│
├──bootstrap            引导程序目录
│  ├──framework         框架类代码
│  │  ├──base.php       框架基础
│  │  ├──base_app.php   如果需要,可以单独定义框架基础
│  │  ├──base_test.php  如果需要,可以单独定义框架基础
│  │  ├──base_cli.php   如果需要,可以单独定义框架基础
│  │  ├──loader.php     自动加载函数
│  │  ├──function.php   框架函数
│  │  ├──user.php       全局函数,常量等用户自定义文件
│  │  └──init.php       框架初始化
│  │
│  ├──app.php        应用模式
│  ├──test.php       测试模式
│  └──cli.php        命令行模式
│
├──config               配置目录
│  ├──default           默认配置
│  └──user              用户配置
│     └──config名字.php 对应的config配置
│
│
├──library              图书馆, 也就是你自己的类文件函数什么的, 怎么加载是你的事
│  └──msqphp
│     └──framework      msqphp框架扩展目录, 万能get, call, callStatic扩展(如果在这里面放类什么的, 将composer autoload中添加psr4:msqphp:当前路径[暂定])
│
├──public                WEB目录（对外访问目录）
│  ├──server.php         apache入口文件 , 载入引导程序
│  └──.htaccess          用于apache的重写
│
├──resources            资源目录
│  ├──language          应用类语言目录  语言/[路由分组/]对应视图.php
│  ├──templates         应用类视图目录  [语言/][主题/][路由分组/]对应视图.php (允许单一视图配置对应语言的视图文件)
│  └──views             基础视图目录目录 error.html 什么的
│
│
├──storage             仓库, 放东西的, 需要对应权限
│  ├──cache             文件缓存目录
│  ├──session           session缓存目录(如果文件的话)
│  ├──log               日志目录(暂未实现)
│  ├──framework         框架缓存目录, 一般会有config缓存,cron缓存,自动加载缓存
│  └──templates         模版目录
│     ├──part           中间件缓存
│     └──package           最终缓存 非静态html 纯php代码+html代码并且经过压缩(也就是没有注释换行什么的文件缓存)
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
│    ├──core             核心类(放置一些核心类,以基类为基础,互相依赖,是框架核心)
│    ├──main             主体类(放置一些核心类,以基类、核心类为基础,互相依赖,主要为应用实现)
│    ├──vendor           扩展类(提供一些不必要但可能使用的功能,例如图片处理,上传功能等等)
│    │
│    ├──resource         基础视图资源,安装资源
│    │
│    ├──test             测试类及框架测试
│    │
│    ├──Environment.php  框架环境类 主运行逻辑负责
│    │
│    ├──App.php          应用方式模式运行
│    ├──Cli.php          命令行模式运行
│    │
│    ├──Framework.php    框架安装
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
│  │  ├──view            视图展示       只展示 或者直接从Action,Model获取展示数据(仅推荐不需要任何参数, 全局一致(语言除外), 并进行html缓存的数据) (推荐视图局部缓存时使用)
│  │  └....
│  └....
~~~

###*单模块mavc:*

* * * * *
~~~
├──application           应用目录
│  ├──route.php          路由流程类
│  ├──controller      逻辑类         主逻辑
│  ├──action          数据处理加工   只加工, 不读取
│  ├──model           数据读取       读和取, 不加工
│  ├──view            视图展示       只展示 或者直接从Action,Model获取展示数据(仅推荐不需要任何参数, 全局一致(语言除外), 并进行html缓存的数据) (推荐视图局部缓存时使用)
│  └....
~~~


* * * * *

###*多模块自定以(简单任性,随意配置):*

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
│  │  │  ├──View.php            视图展示       只展示 或者直接从Action,Model获取展示数据(仅推荐不需要任何参数, 全局一致(语言除外), 并进行html缓存的数据) (推荐视图局部缓存时使用)
│  │  │  └....
│  │  └....
│  └....
~~~


......详情请阅读开发手册 : http://www.kancloud.cn/msqphp/msqphp