<?php


namespace Freezemage\Pizdyk;

use Freezemage\Pizdyk\Observer\Censorship;
use Freezemage\Pizdyk\Vk\LongPoll\Listener;
use Freezemage\Pizdyk\Vk\Message\Collection;
use Freezemage\Pizdyk\Vk\Message\Item;
use Freezemage\Pizdyk\Vk\Message\Service;


final class Engine
{
    private const COOLDOWN = 250000;
    private Listener $server;
    /** @var array<Observer> */
    private array $observers;
    private Service $messageService;

    public function __construct(Listener $server, Service $messageService)
    {
        $this->server = $server;
        $this->messageService = $messageService;
    }

    public function attach(Observer $observer): void
    {
        $this->observers[] = $observer;
    }

    public function run(): void
    {
        while (true) {
            usleep(Engine::COOLDOWN);

            $messages = $this->server->listen();

            $responses = new Collection();
            foreach ($this->observers as $observer) {
                $responses = $responses->merge($observer->update($messages));
            }

            foreach ($responses as $response) {
                $this->messageService->send($response);
                usleep(Engine::COOLDOWN);
            }
        }
    }
}