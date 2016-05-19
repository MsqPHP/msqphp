msqphp beta -2.0(注意是负)
==========================

msqphp is a simple but perfect framework.

msqphp是一个简单但是完美的框架(至少我认为是).

####################################################

##使用须知

开源, 免费, 无需求, 函数随意改, 代码随意拆, 只要你别说这个框架是你自己写的, 是msqphp框架, 那你就随意折腾

当然:如果你想持续使用什么的话, 最好别动框架本身, 那个扩展的地方就够你闹腾了

git : https://github.com/msqphp/msqphp
开发手册 : http://www.kancloud.cn/msqphp/msqphp

####################################################

##要求

+ composer 安装 更新
+ php 7.0及以上
+ mbstring ^_^ 我不会像larvel一样自己写一个强大的mb_string函数库什么的
+ pdo 否则数据库什么的就别想了
+ config 按需合理配置
+ apache 重写规则以及 自定义 入口文件后缀
    
    > 注:因为框架比较强大的视图类(至少我认为很强大), 导致静态html可以不经过apache重写规则直接访问(是不是很强大), 且只需判断一下是否过期就直接返回, 所以入口文件后缀为server.php;


####################################################

##特性


+ 命名空间, 闭包函数一堆
+ 万能get, call, staticcall, 便于扩展

    > 注:结果导致我不确定这个框架是不是面向对象什么的了, 需要一个新功能, 好, 多一个method文件, 返回一个function, 需要一个新引用, 好, 多一个get文件, 返回一个instance, 0.0, 无法想象会变成什么样子
    
+ 伪面向对象, 原因如上
+ 基础类
+ 框架核心类
+ 扩展类
+ composer支持(虽然是废话)(但这真的是一个很大的特性)
+ beta -2.0(注意是负)



####################################################

##注意


+ 只是一个beta -2.0(注意是负), 除版权外, 不负责任何问题
+ 框架未经实际或大范围使用
+ 不断的beta中, 可能会产生 beta -1.0版本什么的
+ 确保所有配置的对应路径或文件存在, 不负责创建什么的
+ 注意以上及一系列未知注意事项

## 完成或未完成(伪成品,差不多可以当成成品使用,但可能还会有更改)
+ cookie(完成)
+ session(完善中)
+ cache(伪成品)
+ route(伪成品)
+ controller(伪成品)
+ model(伪成品)
+ view(伪成品)
+ test类(伪成品)
+ test(完善中)
+ 基类(完善中)
+ 其他(完善中)

####################################################

##安装

+ composer.json require msqphp/framework 版本号??(好像是1.几的beta版本)
+ 配置composer autoload pas-4 "App":"application" , 自己app应用的顶级命名空间位置
+ composer update
+ 引入vendor/msqphp/framework/Framework.php(或者引入composer auto类)
+ 写代码: \msqphp\Framework::install(根目录) (理想状态, 现在是个半成品)
+ 配置入口目录 根目录下public
+ 输入配置的网站(ip地址)开始使用吧.

####################################################

##配置

+ 应用目录下route.php        配置路由流程
+ database     下配置 数据库相关情况
+ 入口文件配置 APP_DEBUG, 以及给 环境类传的 路径配置(如果你改目录的话)
+ 按需合理配置

####################################################

##目录结构(config中大多可配, 改不改是你的事情)


~~~
www  WEB部署目录（或者子目录）
├--composer.json         composer定义文件
├--application           应用目录(顶级命名空间 App)
├  ├--route.php          路由流程类
│  └--[模块目录]         可选
│     ├--Controller      逻辑类     希望且愿望 只存在调用 action model view 或者 session cookie 之类的 类函数, 而几乎不写任何 php原生函数或代码什么的
│     ├--Action          数据处理加工   只加工, 不读取
│     ├--Model           数据读取       读和取, 不加工
│     └--View            视图展示       只展示 或者 从Action Model 获得展示数据(仅推荐不需要任何参数, 全局一致(语言除外), 并进行html缓存的数据)
│
│
├--public                WEB目录（对外访问目录）
│  ├--server.php         apache入口文件 , 载入引导程序
│  └--.htaccess          用于apache的重写
│
├--applicationtests     应用测试目录(暂时无用)
│
│
├--bootstrap            引导程序目录
│  └--app.php           app文件(定义APP_DEBUG, 加载自动加载类, 加载框架并运行框架, 一个自定义的入口文件)
│
│
├--config               配置目录
│  └--config名字.php     对应的config
│
│
├--library              图书馆, 也就是你自己的类文件函数什么的, 怎么加载是你的事
│  └--msqphp            msqphp框架扩展目录, 万能get, call, staticcall扩展(如果在这里面放类什么的, 将composer autoload中添加psr4:当前路径[暂定])
│
│
├--resources            资源目录
│  ├--language          应用类语言目录  [模块]/语言/控制器(不带controller)/对应视图.php
│  ├--templates         应用类视图目录  [模块]/[主题]/[语言]/控制器(不带controller)/对应视图.php (允许单一视图配置对应语言的视图文件)
│  └--views             基础视图目录目录 404.tml 什么的
│
│
├--storage             仓库, 放东西的, 需要对应权限
│  ├--cache             文件缓存目录
│  ├--session           session缓存目录(如果文件的话)
│  ├--log               日志目录(暂未实现)
│  ├--framework         框架缓存目录, 一般会有config缓存, autoload缓存
│  └--templates         模版目录
│     ├--cache          中间件缓存
│     └--last           最终的缓存 非静态html 纯php代码+html代码并且经过压缩(也就是没有注释换行什么的文件缓存)
│
│--vendor               composer扩展文件夹(加载什么是你的事)
│  └--msqphp            msqphp框架扩展什么的
~~~


### 命名规范

+ 命名空间 小驼峰

+ 目录     小驼峰 例: home/controller(不要问为什么, 只是这样路由功能可以强大很多很多[毕竟不需要区分大小写什么的了])

+ declare(strict_types = 1); 全部开启, 不开启, 你标注标量什么的会有更大的问题

+ 文件名   类文件大驼峰, 过程形文件小驼峰, 后缀.php

+ 类       大驼峰 例 Index, UserView, 

+ 类函数   小驼峰  例  login, loginWithCookie

+ 类属性   小写+下划线 例 username, cookif_info (不推荐下划线开头)

+ 函数     小驼峰

+ 常量     随意, 你分的清就好(推荐纯大写加下划线分割, 例:PHP_COSTANT);

+ 数据库   暂未完善

+ 数组     [ ], 不推荐或避免使用[), 无论多维还是一维;

+ 闭包函数  function ($arg1, $arg2) use ($arg3, $arg4) {
    
            };

+ 缩进     4个空格, 原因, 便于空格控制缩进

+ 函数标量 除了miexd, 以及void以外, 尽量全部添加, 避免类型转换错误

+ 除此之外 大概就是psr什么的吧.随意

