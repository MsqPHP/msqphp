<?php declare(strict_types = 1);
namespace msqphp\core\controller;

use msqphp\base;

return function(string $url, int $time = 0, string $msg = '')
{
    return core\response\Response::jump($url, $time, $msg);
};