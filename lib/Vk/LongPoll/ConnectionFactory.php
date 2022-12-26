<?php


namespace Freezemage\Pizdyk\Vk\LongPoll;

interface ConnectionFactory
{
    public function createConnection(): Connection;
}