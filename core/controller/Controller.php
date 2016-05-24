<?php declare(strict_types = 1);
namespace msqphp\core\controller;

use msqphp\traits;

abstract class Controller
{
    use traits\Get,traits\Call;
}