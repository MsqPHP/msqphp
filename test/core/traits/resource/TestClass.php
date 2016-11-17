<?php declare(strict_types = 1);
namespace msqphp\test\traits\resource;

use \msqphp\core\traits;

class TestClass
{
    use traits\Call;
    use traits\Get;
    use traits\CallStatic;
}