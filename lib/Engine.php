<?php


namespace Freezemage\Pizdyk;

use Freezemage\Pizdyk\Output\Responder;
use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Listener;
use Freezemage\Pizdyk\Vk\Message\Service;


final class Engine
{
    private const DELAY = 250000;
    private Listener $server;
    /** @var array<ObserverInterface> */
    private array $observers;
    private Service $messageService;

    public function __construct(Listener $server, Service $messageService)
    {
        $this->server = $server;
        $this->messageService = $messageService;
    }

    public function attach(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function run(): void
    {
        $responder = new Responder($this->messageService, Engine::DELAY);

        while (true) {
            usleep(Engine::DELAY);

            $events = $this->server->listen();

            $responses = new ResponseCollection();
            foreach ($this->observers as $observer) {
                $responses = $responses->merge($observer->update($events));
            }

            $responder->respond($responses);
        }
    }
}