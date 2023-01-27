<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Configuration\Assets\Audios;
use Freezemage\Pizdyk\Configuration\Assets\Photos;
use Freezemage\Pizdyk\Output\ReplyTarget\MentionedEntity;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Statistics\Facade as StatisticsFacade;
use Freezemage\Pizdyk\Statistics\Item;
use Freezemage\Pizdyk\Statistics\Repository as StatisticsRepository;
use Freezemage\Pizdyk\Statistics\Top\Item as StatisticsTop;
use Freezemage\Pizdyk\Statistics\Top\Repository as StatisticsTopRepository;
use Freezemage\Pizdyk\Utility\Random;
use Freezemage\Pizdyk\Vk\Message\Item as Message;


final class ForcedCensorship implements Command
{
    private Photos $photos;
    private Audios $audios;
    private StatisticsFacade $statisticsFacade;

    public function __construct(Photos $photos, Audios $audios, StatisticsFacade $statisticsFacade)
    {
        $this->photos = $photos;
        $this->audios = $audios;
        $this->statisticsFacade = $statisticsFacade;
    }

    public function getName(): string
    {
        return 'forced_censorship';
    }

    public function getAliases(): array
    {
        return ['force', 'вломить-пиздык', 'вломить', 'пиздык'];
    }

    public function process(Message $message, array $arguments = []): ?Response
    {
        if (empty($arguments['target'])) {
            return new Response(
                    $message->peerId,
                    new User($message->senderId),
                    "Кому пиздык-то вломить?",
                    []
            );
        }

        $photo = Random::pick($this->photos->forcedCensorship);
        if (!is_array($photo)) {
            $photo = [$photo];
        }

        $audio = Random::pick($this->audios->forcedCensorship);
        if (!is_array($audio)) {
            $audio = [$audio];
        }

        $this->statisticsFacade->track($message->peerId, "Пиздыков выдано лично сержантами");

        if (preg_match('/\[id(.*)\|.+\]/', $arguments['target'], $matches)) {
            $userId = $matches[1];

            if (is_numeric($userId)) {
                $userId = (int) $userId;
                $this->statisticsFacade->trackUser($message->peerId, $userId);
            }
        }

        return new Response(
                $message->peerId,
                new MentionedEntity($arguments['target']),
                '',
                array_merge($photo, $audio)
        );
    }

    public function getDescription(): string
    {
        return 'Forced censorship.';
    }

    public function getArguments(): array
    {
        return ['target' => '\[.+\|.+\]'];
    }
}