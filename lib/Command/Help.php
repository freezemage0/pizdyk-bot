<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Vk\Message\Item as Message;


final class Help implements Command
{
    private array $prefixes;
    /**
     * @var Command[]
     */
    private array $commands;

    public function __construct(array $prefixes, array $commands)
    {
        $this->commands = $commands;
        $this->prefixes = $prefixes;
    }

    public function getName(): string
    {
        return 'help';
    }

    public function getDescription(): string
    {
        return 'Выводит эту справку';
    }

    public function getArguments(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return ['help', 'помощь', 'хелп'];
    }

    public function process(Message $message): ?Response
    {
        $prefixes = implode(' | ', $this->prefixes);

        $response = [
                "Для выполнения команды, нужно сначала обратиться ко мне",
                "Список обращений, который я понимаю:\n {$prefixes}",
                '',
                "Набор команд, которые я понимаю:"
        ];
        foreach ($this->commands as $command) {
            $argumentList = array_map(
                    fn (string $argumentName): string => "[{$argumentName}]",
                    array_keys($command->getArguments())
            );

            $aliases = implode(' | ', $command->getAliases());
            $argumentList = implode(' ', $argumentList);
            $response[] = "{$aliases} {$argumentList}: {$command->getDescription()}";
        }
        $aliases = implode(' | ', $this->getAliases());
        $response[] = "{$aliases}: {$this->getDescription()}";

        return new Response(
                $message->peerId,
                new User($message->senderId),
                implode("\n", $response)
        );
    }
}