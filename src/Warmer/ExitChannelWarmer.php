<?php

namespace GearDev\Swow\Warmer;
use GearDev\Core\Attributes\Warmer;
use GearDev\Core\ContextStorage\ContextStorage;
use GearDev\Core\Warmers\WarmerInterface;
use GearDev\Coroutines\Co\ChannelFactory;
use GearDev\Coroutines\Co\CoFactory;

#[Warmer]
class ExitChannelWarmer implements WarmerInterface
{

    public function warm(): void
    {
        $this->waitExitSignal();
    }

    public function createExitChannel() {
        $exitControlChannel = ChannelFactory::createChannel(1);
        ContextStorage::setSystemChannel('exitChannel', $exitControlChannel);
        return $exitControlChannel;
    }

    /**
     * @return void
     */
    public function waitExitSignal(): void
    {
        $co = CoFactory::createCo('exitSignal');
        $co->charge(function() {
            $exitControlChannel = $this->createExitChannel();
            \Swow\Coroutine::run(static function () use ($exitControlChannel): void {
                \Swow\Signal::wait(\Swow\Signal::INT);
                $exitControlChannel->push(\Swow\Signal::TERM);
            });
            \Swow\Coroutine::run(static function () use ($exitControlChannel): void {
                \Swow\Signal::wait(\Swow\Signal::TERM);
                $exitControlChannel->push(\Swow\Signal::TERM);
            });
            $code = $exitControlChannel->pop();
            exit($code);
        });


    }
}
