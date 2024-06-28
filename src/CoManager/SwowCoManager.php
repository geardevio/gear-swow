<?php

namespace GearDev\Swow\CoManager;

use GearDev\Coroutines\Interfaces\CoManagerInterface;
use Swow\Coroutine;

class SwowCoManager implements CoManagerInterface
{

    private static $instance;

    private function __construct()
    {
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getCurrentCoroutineId(): int
    {
        return Coroutine::getCurrent()->getId();
    }

    public static function getCoroutineCount(): int
    {
        return Coroutine::count();
    }
}