<?php


namespace Freezemage\Pizdyk\Output;


use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\Message\Item;
use Freezemage\Pizdyk\Vk\Message\Service as MessageService;


class Responder
{
    private MessageService $messageService;
    private int $delay;

    public function __construct(MessageService $messageService, int $delay)
    {
        $this->messageService = $messageService;
        $this->delay = $delay;
    }

    public function respond(ResponseCollection $collection): void
    {
        foreach ($collection as $item) {
            $item = new Item(
                    $item->peerId,
                    null,
                    "{$item->replyTag}\n{$item->body}",
                    Item::DIRECTION_OUT,
                    time(),
                    $item->attachments
            );

            $this->messageService->send($item);
            usleep($this->delay);
        }
    }
}