<?php


namespace Freezemage\Pizdyk\Output;

class Response
{
    public const REPLY_TO_ALL = -1;

    public string $peerId;
    public array $attachments;
    public string $body;
    public string $replyTo;

    public function __construct(string $peerId, string $replyTo, string $body, array $attachments)
    {
        $this->peerId = $peerId;
        $this->replyTo = $replyTo;
        $this->body = $body;
        $this->attachments = $attachments;
    }
}