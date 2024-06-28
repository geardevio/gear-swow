<?php

namespace GearDev\Swow\Co;

use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Coroutines\Interfaces\AbstractCo;
use Swow\Coroutine;
use Swow\Sync\WaitGroup;

class Co extends AbstractCo
{
    private function getWaitGroup(): ?WaitGroup {
        if ($this->sync===true) {
            $waitGroup = new WaitGroup();
            $waitGroup->add();
        } else {
            $waitGroup = null;
        }
        return $waitGroup;
    }

    protected function runCoroutine(bool $sync = false) {
        $waitGroup = $this->getWaitGroup();
        $coroutine = new Coroutine(function ($callable, $processName, $delay, ...$args) use ($waitGroup) {
            ContextStorage::setCurrentRoutineName($processName);
            if ($delay > 0) {
                sleep($delay);
            }

            $callable(...$args);
            ContextStorage::clearStorage();
            $waitGroup?->done();
        });
        $coroutine->resume($this->function, $this->name, $this->delaySeconds, ...$this->args);
        $waitGroup?->wait();
    }
}
