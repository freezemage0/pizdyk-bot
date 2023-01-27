<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Statistics\Facade;
use Freezemage\Pizdyk\Statistics\Repository;
use Freezemage\Pizdyk\Statistics\Top\Item;
use Freezemage\Pizdyk\Statistics\Top\Repository as TopRepository;
use Freezemage\Pizdyk\Vk\Message\Item as Message;
use Freezemage\Pizdyk\Vk\User\Service as UserService;


final class Statistics implements Command
{
    private Facade $statisticsFacade;
    private UserService $userService;

    public function __construct(Facade $statisticsFacade, UserService $userService)
    {
        $this->statisticsFacade = $statisticsFacade;
        $this->userService = $userService;
    }

    public function getName(): string
    {
        return 'statistics';
    }

    public function getDescription(): string
    {
        return 'Показывает статистику за всё время.';
    }

    public function getArguments(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return ['stats', 'statistics', 'статистика', 'статы'];
    }

    public function process(Message $message): ?Response
    {
        $items = $this->statisticsFacade->get($message->peerId);

        $result = ["Глобальная статистика:"];
        foreach ($items['global'] as $item) {
            $result[] = "{$item->name}: {$item->counter}";
        }

        $result[] = "\nТоп-10 мыслепреступников:";

        $users = array_map(fn (Item $item): int => $item->userId, $items['users']);
        $users = $this->userService->get(...$users);

        foreach ($items['users'] as $item) {
            $user = $users[$item->userId];
            $userName = "{$user->firstName} {$user->lastName}";

            $result[] = "{$userName}: {$item->counter} пиздыков";
        }

        return new Response(
                $message->peerId,
                new User($message->senderId),
                implode("\n", $result)
        );
    }
}