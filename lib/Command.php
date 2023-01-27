<?php


namespace Freezemage\Pizdyk;

use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Vk\Message\Item as Message;


interface Command
{
    public function getName(): string;

    public function getDescription(): string;

    public function getArguments(): array;

    public function getAliases(): array;

    public function process(Message $message): ?Response;
}