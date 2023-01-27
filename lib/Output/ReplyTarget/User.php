<?php


namespace Freezemage\Pizdyk\Output\ReplyTarget;

use Freezemage\Pizdyk\Output\ReplyTarget;


final class User implements ReplyTarget
{
    private int $id;
    private string $handle;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setHandle(string $handle): void
    {
        $this->handle = $handle;
    }

    public function getMentionTag(): string
    {
        $handle = $this->handle ?? $this->id;
        return "@{$handle}";
    }
}