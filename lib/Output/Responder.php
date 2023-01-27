<?php


namespace Freezemage\Pizdyk\Output;


use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\Message\Item;
use Freezemage\Pizdyk\Vk\Message\Service as MessageService;
use Freezemage\Pizdyk\Vk\User\All;
use Freezemage\Pizdyk\Vk\User\Item as User;
use Freezemage\Pizdyk\Vk\User\Service as UserService;


class Responder
{
    private MessageService $messageService;
    private UserService $userService;
    private int $delay;
    private array $users = [];

    public function __construct(MessageService $messageService, UserService $userService, int $delay)
    {
        $this->messageService = $messageService;
        $this->delay = $delay;
        $this->userService = $userService;
    }

    public function respond(ResponseCollection $collection): void
    {
        foreach ($collection as $item) {
            if ($item->replyTo instanceof ReplyTarget\User) {
                $user = $this->getUser($item->replyTo->getId());
                $item->replyTo->setHandle($user->handle);
            }

            $item = new Item(
                    $item->peerId,
                    null,
                    "{$item->replyTo->getMentionTag()}\n{$item->body}",
                    Item::DIRECTION_OUT,
                    time(),
                    $item->attachments
            );

            $this->messageService->send($item);
            usleep($this->delay);
        }
    }

    private function getUser(int $id): User
    {
        if (!isset($this->users[$id])) {
            $this->users[$id] = $this->userService->get($id);
        }

        return $this->users[$id];
    }
}