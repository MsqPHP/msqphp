<?php declare(strict_types = 1);
namespace msqphp\core\controller;

use msqphp\base;
use msqphp\core;

return function(string $msg, string $url = '', $charset='utf-8')
{
    return base\response\Response::alert($msg, $url, $charset);
};