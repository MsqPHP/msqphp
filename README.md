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

+ git : https://github.com/MsqPHP/msqphp
+ 开发手册 : http://www.kancloud.cn/msqphp/msqphp
+ 邮箱:msq_mengsheng@163.com(偶尔登录,拒绝骚扰)
+ qq群:566103528

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
├──public                WEB目录（对外访问目录）
│  ├──server.php         apache入口文件 , 载入引导程序
│  └──.htaccess          用于apache的重写
│
├──applicationtests     应用测试目录(暂时无用)
│  └──test.php          测试逻辑文件
│
├──bootstrap            引导程序目录
│  └──app.php           app文件(定义APP_DEBUG, 加载自动加载类, 加载框架并运行框架, 一个自定义的入口文件)
│
│
├──config               配置目录
│  └──config名字.php    对应的config
│
│
├──library              图书馆, 也就是你自己的类文件函数什么的, 怎么加载是你的事
│  └──msqphp
|     └──framework      msqphp框架扩展目录, 万能get, call, callStatic扩展(如果在这里面放类什么的, 将composer autoload中添加psr4:msqphp:当前路径[暂定])
│
│
├──resources            资源目录
│  ├──language          应用类语言目录  语言/[模块]/对应视图.php
│  ├──templates         应用类视图目录  [主题]/[模块]/[语言]/对应视图.php (允许单一视图配置对应语言的视图文件)
│  └──views             基础视图目录目录 404.tml 什么的
│
│
├──storage             仓库, 放东西的, 需要对应权限
│  ├──cache             文件缓存目录
│  ├──session           session缓存目录(如果文件的话)
│  ├──log               日志目录(暂未实现)
│  ├──framework         框架缓存目录, 一般会有config缓存, 视图缓存
│  └──templates         模版目录
│     ├──cache          中间件缓存
│     └──last           最终的缓存 非静态html 纯php代码+html代码并且经过压缩(也就是没有注释换行什么的文件缓存)
│
│──vendor               composer扩展文件夹(加载什么是你的事)
│  └──msqphp            msqphp框架
│
├──composer.json         composer定义文件
~~~


......详情请阅读开发手册 : http://www.kancloud.cn/msqphp/msqphp