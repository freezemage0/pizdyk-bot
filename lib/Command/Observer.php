<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Censorship\Constraint\IsIncoming;
use Freezemage\Pizdyk\Command\Constraint\HasMention;
use Freezemage\Pizdyk\ObserverInterface;
use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection as EventCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Type\NewMessage;


final class Observer implements ObserverInterface
{
    private array $commands;

    public function __construct(array $commands)
    {
        $this->commands = $commands;
    }

    public function update(EventCollection $collection): ResponseCollection
    {
        $collection = $collection->filter(new IsIncoming())->filter(new HasMention());

        foreach ($collection as $event) {
            /** @var NewMessage $event */
            $message = $event->getEntity();

            
        }
    }
}