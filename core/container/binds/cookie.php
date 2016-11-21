<?php declare(strict_types = 1);
namespace msqphp\core\container;

use msqphp\main;

return [
    'shared' => false,
    'object' => function () {
        return new main\cookie\Cookie();
    },
];