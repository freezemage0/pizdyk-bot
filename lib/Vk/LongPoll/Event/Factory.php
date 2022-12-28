<?php


namespace Freezemage\Pizdyk\Vk\LongPoll\Event;

use Freezemage\Pizdyk\Vk\LongPoll\Event\Type\NewMessage;
use Freezemage\Pizdyk\Vk\Message\Item;
use RuntimeException;
use UnexpectedValueException;


class Factory
{
    public function create(string $type, string $eventId, string $version, array $object): Update {
        switch ($type) {
            case 'message_new':
                if (!isset($object['message'])) {
                    throw new UnexpectedValueException('Events of type "new_message" are expected to have message object.');
                }

                $message = $object['message'];
                return new NewMessage(
                        $eventId,
                        $version,
                        new Item(
                                $message['peer_id'],
                                $message['from_id'],
                                $message['text'],
                                $message['out'],
                                $message['date'],
                                $message['attachments']
                        )
                );
            default:
                throw new RuntimeException('Unknown event type.');
        }
    }
}