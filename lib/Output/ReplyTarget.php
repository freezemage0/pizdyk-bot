<?php


namespace Freezemage\Pizdyk\Output;

interface ReplyTarget
{
    public function getMentionTag(): string;
}