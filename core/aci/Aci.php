<?php declare(strict_types = 1);
namespace msqphp\core\aci;

final class Aci
{
    private $roles = [];
    private $resources = [];
    private $allowed = [];
    private $denied = [];

    private $assertions = [
        'allowed'=>[],
        'denied'=>[]
    ];

    private $pointer = [];
    public function __construct()
    {
    }
}