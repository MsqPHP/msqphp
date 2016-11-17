<?php declare(strict_types = 1);
namespace msqphp\main\controller;

use msqphp\core\traits;

abstract class Controller
{
    use traits\Get;
    use traits\Call;
}