<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Configuration\Assets\Audios;
use Freezemage\Pizdyk\Configuration\Assets\Photos;
use Freezemage\Pizdyk\Output\ReplyTarget\MentionedEntity;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Utility\Random;
use Freezemage\Pizdyk\Vk\Message\Item as Message;


final class ForcedCensorship implements Command
{
    private Photos $photos;
    private Audios $audios;

    public function __construct(Photos $photos, Audios $audios)
    {
        $this->photos = $photos;
        $this->audios = $audios;
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