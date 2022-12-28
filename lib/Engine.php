<?php


namespace Freezemage\Pizdyk;

use Freezemage\Pizdyk\Output\Responder;
use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Listener;


final class Engine
{
    public const DELAY = 250000;
    private Listener $server;
    /** @var array<ObserverInterface> */
    private array $observers;
    private Responder $responder;

    public function __construct(Listener $server, Responder $responder)
    {
        $this->server = $server;
        $this->responder = $responder;
    }

    public function attach(ObserverInterface $observer): void
    {
        $this->observers[] = $observer;
    }

    public function run(): void
    {
        while (true) {
            usleep(Engine::DELAY);

            $events = $this->server->listen();

            $responses = new ResponseCollection();
            foreach ($this->observers as $observer) {
                $responses = $responses->merge($observer->update($events));
            }

            $this->responder->respond($responses);
        }
    }
}