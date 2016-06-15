<?php declare(strict_types = 1);
namespace msqphp\core\route;

Route::addRoule('*',function ($value) {
    return true;
});
Route::addRoule(':int', function (& $value) {
    if (0 !== preg_match('/^\d+$/',$value)) {
        $value = (int) $value;
        return true;
    } else {
        return false;
    }
});
Route::addRoule(':empty', function ($value) {
    return $value === '';
});
Route::addGroup([
    'name'      =>'module',
    'allowed'   =>['home'],
    'default'   =>'home',
    'namespace' =>true,
]);


Route::group('module','home',function(){
    //controller模块
    Route::addGroup([
        'name'      =>'controller',
        'allowed'   =>['index','user'],
        'default'   =>'index',
        'namespace' =>'controller'
    ]);

    Route::group('controller','index', function(){
        Route::get('index', function() {
            echo 'Welcome To Msqphp';
        });
        Route::get(':empty', 'Index@index');
    });

    Route::group('controller', 'user', function() {
        Route::get('index', 'User@index');

    });
});

Route::error(function () {
    echo '匹配失败';
});