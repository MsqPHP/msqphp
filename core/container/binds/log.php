<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\main;

return [
    'shared' => true,
    'object' => function () {
        return new main\log\Log;
    },
];
