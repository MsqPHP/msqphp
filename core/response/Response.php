<?php declare(strict_types = 1);
namespace msqphp\core\response;

use msqphp\core\traits;

final class Response
{
    use traits\CallStatic;
    use ResponseJumpTrait;
    use ResponseDumpTrait;
}