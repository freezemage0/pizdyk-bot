<?php


namespace Freezemage\Pizdyk\Output\ReplyTarget;


use Freezemage\Pizdyk\Output\ReplyTarget;


final class MentionedEntity implements ReplyTarget
{
    private string $mention;
    private string $id;
    private string $handle;

    public function __construct(string $mention)
    {
        $this->mention = $mention;
    }

    public function getId(): string
    {
        if (!isset($this->id)) {
            $this->parse();
        }

        return $this->id;
    }

    public function getHandle(): string
    {
        if (!isset($this->handle)) {
            $this->parse();
        }

        return $this->handle;
    }

    public function getMentionTag(): string
    {
        $mention = trim($this->mention, '[]');
        list($identity, $handle) = explode('|', $mention);

        return "@{$identity} ({$handle})";
    }

    private function parse(): void
    {
        $mention = trim($this->mention, '[]');
        list($identity, $handle) = explode('|', $mention);

        $this->id = $identity;
        $this->handle = $handle;
    }
}