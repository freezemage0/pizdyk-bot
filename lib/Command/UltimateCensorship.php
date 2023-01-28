<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Output\ReplyTarget\MentionedEntity;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Statistics\Facade as StatisticsFacade;
use Freezemage\Pizdyk\Vk\Message\Item as Message;


final class UltimateCensorship implements Command
{
    private Configuration $configuration;
    private StatisticsFacade $statisticsFacade;

    public function __construct(StatisticsFacade $statisticsFacade, Configuration $configuration)
    {
        $this->statisticsFacade = $statisticsFacade;
        $this->configuration = $configuration;
    }

    public function getName(): string
    {
        return 'ultimate_censorship';
    }

    public function getDescription(): string
    {
        return 'Выдать ультимативный пиздык (только при его наличии).';
    }

    public function getArguments(): array
    {
        return ['target' => '\[.+\|.+\]'];
    }

    public function getAliases(): array
    {
        return ['ульт', 'ульта', 'ult', 'ultimate', 'ulti'];
    }

    public function process(Message $message, array $arguments = []): ?Response
    {
        foreach ($this->configuration->getUltimates() as $ultimate) {
            if ($ultimate->userId != $message->senderId) {
                continue;
            }

            $replyTo = new MentionedEntity($arguments['target']);
            $this->statisticsFacade->track($message->peerId, "Прожато ультов");

            if (preg_match('/\[id(.*)\|.+\]/', $arguments['target'], $matches)) {
                $userId = (int) $matches[1];
                $this->statisticsFacade->trackUser($message->peerId, $userId);
            }

            return new Response(
                    $message->peerId,
                    $replyTo,
                    $ultimate->description,
                    $ultimate->assets
            );
        }

        return new Response($message->peerId, new User($message->senderId), "Нечем бить, ульт не определён.");
    }
}