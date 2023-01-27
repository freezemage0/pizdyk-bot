<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Vk\Message\Item as Message;


class Dump implements Command
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getName(): string
    {
        return 'dump';
    }

    public function getDescription(): string
    {
        return 'Вывод статистики и отладочных данных.';
    }

    public function getArguments(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return ['dump', 'dump-configuration', 'дамп', 'дамп-конфигурации'];
    }

    public function process(Message $message): ?Response
    {
        $requestedBy = $message->senderId;
        $peer = $message->peerId;

        $message = [
                'prefixes' => implode('/', $this->configuration->getPrefixes()),
                'api.version' => $this->configuration->getApi()->version,
                'api.maxLongPollAttempts' => $this->configuration->getApi()->maxLongPollAttempts,
                'engine.ignoredUsers' => implode('/', $this->configuration->getIgnoredUsers()),
                'aoe.maxPeriod' => $this->configuration->getAreaOfEffect()->maxPeriod,
                'aoe.maxProcCount' => $this->configuration->getAreaOfEffect()->maxProcCount,
                'ruleset.stems' => implode('/', $this->configuration->getRuleset())
        ];

        $result = [];
        foreach ($message as $config => $value) {
            $result[] = "{$config}: $value";
        }

        return new Response(
            $peer,
            new User($requestedBy),
            implode("\n", $result),
            []
        );
    }
}