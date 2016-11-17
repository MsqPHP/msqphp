<?php declare(strict_types = 1);
namespace msqphp\main\controller;

use msqphp\core;

return function(string $url, int $time = 0, string $msg = '')
{
    return core\response\Response::jump($url, $time, $msg);
};