<?php declare(strict_types = 1);
namespace msqphp\main\controller;

use msqphp\core;

return function(string $msg, string $url = '', $charset='utf-8')
{
    return core\response\Response::alert($msg, $url, $charset);
};