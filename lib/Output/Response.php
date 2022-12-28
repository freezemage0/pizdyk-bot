<?php


namespace Freezemage\Pizdyk\Output;

class Response
{
    public string $peerId;
    public array $attachments;
    public string $body;
    public string $replyTag;

    public function __construct(string $peerId, string $replyTag, string $body, array $attachments)
    {
        $this->peerId = $peerId;
        $this->replyTag = $replyTag;
        $this->body = $body;
        $this->attachments = $attachments;
    }
}