<?php declare(strict_types = 1);
namespace msqphp\core\controller;

use msqphp\base;

return function()
{
    return base\ip\Ip::get();
};