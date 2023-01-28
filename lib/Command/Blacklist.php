<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Blacklist\Storage;
use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Command\Constraint\CanBeUsed;
use Freezemage\Pizdyk\Configuration\Blacklist as BlacklistConfiguration;
use Freezemage\Pizdyk\Output\ReplyTarget\MentionedEntity;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Vk\Message\Item as Message;
use Freezemage\Pizdyk\Vk\User\Service as UserService;


final class Blacklist implements Command
{
    private Storage $storage;
    private UserService $userService;
    private BlacklistConfiguration $configuration;

    public function __construct(Storage $storage, UserService $userService, BlacklistConfiguration $configuration)
    {
        $this->storage = $storage;
        $this->userService = $userService;
        $this->configuration = $configuration;
    }

    public function getName(): string
    {
        return 'blacklist';
    }

    public function getDescription(): string
    {
        return 'Управление чёрным списком. [action] должен быть "добавить", "убрать" или "показать".';
    }

    public function getArguments(): array
    {
        return ['action' => 'добавить|убрать|показать', 'target' => '\s+\[.+\|.+\]'];
    }

    public function getAliases(): array
    {
        return ['blacklist', 'ignore', 'игнор', 'черный-список'];
    }

    public function process(Message $message, array $arguments = []): ?Response
    {
        $constraint = new CanBeUsed($this->configuration->canBeUserBy);
        if (!$constraint->evaluate($message)) {
            return new Response(
                    $message->peerId,
                    new User($message->senderId),
                    "Ты меня не заставишь"
            );
        }

        $action = mb_strtolower($arguments['action']);
        if ($action == 'показать') {
            $users = $this->userService->get(...$this->storage->get($message->peerId));
            $response = ['Жители черного списка:'];
            foreach ($users as $item) {
                $response[] = "{$item->firstName} {$item->lastName}";
            }

            return new Response(
                    $message->peerId,
                    new User($message->senderId),
                    implode("\n", $response)
            );
        }

        if (empty($arguments['target'])) {
            return new Response(
                    $message->peerId,
                    new User($message->senderId),
                    "Кого добавить-то?"
            );
        }

        $arguments['target'] = trim($arguments['target']);

        $mention = new MentionedEntity($arguments['target']);
        $identity = $mention->getId();
        if (!str_starts_with($identity, 'id')) {
            return new Response($message->peerId, new User($message->senderId), "В черный список можно добавлять только пользователей.");
        }

        $identity = mb_substr($identity, 2);

        if ($action == 'добавить') {
            $this->storage->add($message->peerId, $identity);
            return new Response(
                    $message->peerId,
                    new User($message->senderId),
                    "{$mention->getMentionTag()} добавлен в черный список."
            );
        }

        if ($action == 'убрать') {
            $this->storage->delete($message->peerId, $identity);
            return new Response(
                    $message->peerId,
                    new User($message->senderId),
                    "{$mention->getMentionTag()} удален из черного списка."
            );
        }


        return new Response(
                $message->peerId,
                new User($message->senderId),
                'Не понял чего ты от меня хочешь. Могу добавить, удалить и показать.'
        );
    }
}