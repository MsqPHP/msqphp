<?php declare(strict_types = 1);
namespace msqphp\core\controller;

use msqphp\base;
use msqphp\core;

return function()
{
    return base\ip\Ip::get();
};