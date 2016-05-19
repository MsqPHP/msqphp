<?php declare(strict_types = 1);
namespace msqphp\core\controller;

use msqphp\base;
use msqphp\core;
use msqphp\vendor;

return function()
{
    return vendor\ip\Ip::getInt();
};