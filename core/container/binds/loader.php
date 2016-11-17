<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\core;

return [
    'shared' => false,
    'object' => function () {
        return new core\loader\Loader();
    },
];