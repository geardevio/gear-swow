<?php

namespace GearDev\Swow\Co;

use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Coroutines\Interfaces\AbstractCo;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Facade;
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
        if ($this->needCloneDiContainer) {
            $oldContainer = ContextStorage::getMainApplication();
            $newContainer = clone ($oldContainer);
        } else {
            $newContainer = ContextStorage::getApplication();
            $oldContainer=null;
        }
        $oldCurrentCoroutineId = Coroutine::getCurrent()->getId();
        $coroutine = new Coroutine(function ($callable, Application $newContainer, $processName, $delay, $oldContainer, ...$args) use ($waitGroup, $oldCurrentCoroutineId) {
            ContextStorage::cloneLogContextFromFirstCoroutineToSecond($oldCurrentCoroutineId, Coroutine::getCurrent()->getId());
            ContextStorage::setCurrentRoutineName($processName);
            ContextStorage::setApplication($newContainer);
            if ($this->needCloneDiContainer && $oldContainer) {
                $newContainer->instance('app', $newContainer);
                $newContainer->instance(Application::class, $newContainer);
                $newContainer->instance(Container::class, $newContainer);
                Container::setInstance($newContainer);

                Facade::clearResolvedInstances();
                Facade::setFacadeApplication($newContainer);

            }

            if ($delay > 0) {
                sleep($delay);
            }

            $callable(...$args);
            ContextStorage::clearStorage();
            $waitGroup?->done();
        });
        $coroutine->resume($this->function, $newContainer, $this->name, $this->delaySeconds, $oldContainer??null, ...$this->args);
        $waitGroup?->wait();
    }
}
