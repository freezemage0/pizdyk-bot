<?php


namespace Freezemage\Pizdyk;


use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection as EventCollection;


interface ObserverInterface
{
    public function update(EventCollection $collection): ResponseCollection;
}