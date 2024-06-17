<?php

namespace GearDev\Swow\Channel;

use GearDev\Coroutines\Interfaces\ChannelInterface;

class SwowChannel implements ChannelInterface
{

    private \Swow\Channel $channel;

    public function __construct(int $capacity = 0)
    {
        $this->channel = new \Swow\Channel($capacity);
    }

    public function push(mixed $data, int $timeout = -1): static
    {
        $this->channel->push($data, $timeout);
        return $this;
    }

    public function pop(int $timeout = -1): mixed
    {
        return $this->channel->pop($timeout);
    }

    public function close(): void
    {
        $this->channel->close();
    }

    public function getCapacity(): int
    {
        return $this->channel->getCapacity();
    }

    public function getLength(): int
    {
        return $this->channel->getLength();
    }

    public function isAvailable(): bool
    {
        return $this->channel->isAvailable();
    }

    public function hasProducers(): bool
    {
        return $this->channel->hasProducers();
    }

    public function hasConsumers(): bool
    {
        return $this->channel->hasConsumers();
    }

    public function isEmpty(): bool
    {
        return $this->channel->isEmpty();
    }

    public function isFull(): bool
    {
        return $this->channel->isFull();
    }

    public function isReadable(): bool
    {
        return $this->channel->isReadable();
    }

    public function isWritable(): bool
    {
        return $this->channel->isWritable();
    }

    public function __debugInfo(): array
    {
        return $this->channel->__debugInfo();
    }
}