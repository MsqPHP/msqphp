<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\core;

return [
    'shared' => true,
    'object' => function () {
        return core\config\Config::getInstance();
    },
];
