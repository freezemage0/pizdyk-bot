<?php


namespace Freezemage\Pizdyk\Debug;

use Freezemage\Pizdyk\ObserverInterface;
use Freezemage\Pizdyk\Output\ReplyTarget\All;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection as EventCollection;


final class Observer implements ObserverInterface
{
    public function update(EventCollection $collection): ResponseCollection
    {
        $rc = new ResponseCollection();

        foreach ($collection as $item) {
            $e = $item->getItem();

            $rc->push(new Response(
                    $e->peerId,
                    new All(),
                    'Debug reply with audio attachment',
                    ['audio-217965365_456239017']
            ));
        }

        return $rc;
    }
}