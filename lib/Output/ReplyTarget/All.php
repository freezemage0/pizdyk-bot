<?php


namespace Freezemage\Pizdyk\Output\ReplyTarget;

use Freezemage\Pizdyk\Output\ReplyTarget;


final class All implements ReplyTarget
{
    public function getMentionTag(): string
    {
        return '@all';
    }
}