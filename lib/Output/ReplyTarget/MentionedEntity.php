<?php


namespace Freezemage\Pizdyk\Output\ReplyTarget;

use Freezemage\Pizdyk\Output\ReplyTarget;


final class MentionedEntity implements ReplyTarget
{
    private string $mention;

    public function __construct(string $mention)
    {
        $this->mention = $mention;
    }

    public function getMentionTag(): string
    {
        $mention = trim($this->mention, '[]');
        list($identity, $handle) = explode('|', $mention);

        return "@{$identity} ({$handle})";
    }
}