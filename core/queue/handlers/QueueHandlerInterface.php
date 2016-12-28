<?php declare(strict_types = 1);
namespace msqphp\core\queue\handlers;

interface QueueHandlerInterface
{
    public function __construct(array $config);

    public function in(string $info);

    public function out() : ?string;

    public function length() : int;
}