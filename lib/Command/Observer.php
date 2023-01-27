<?php


namespace Freezemage\Pizdyk\Command;

use Freezemage\Pizdyk\Censorship\Constraint\IsIncoming;
use Freezemage\Pizdyk\Command;
use Freezemage\Pizdyk\Command\Constraint\HasMention;
use Freezemage\Pizdyk\ObserverInterface;
use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection as EventCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Type\NewMessage;


final class Observer implements ObserverInterface
{
    private array $prefixes;
    /** @var array<Command> */
    private array $commands;

    private string $pattern;

    public function __construct(array $prefixes, array $commands)
    {
        $this->prefixes = $prefixes;
        $this->commands = $commands;
    }

    public function update(EventCollection $collection): ResponseCollection
    {
        $collection = $collection
                ->filter(new IsIncoming())
                ->filter(new HasMention($this->prefixes));

        if (!isset($this->pattern)) {
            $commandsPattern = [];
            foreach ($this->commands as $command) {
                $arguments = [];
                foreach ($command->getArguments() as $name => $pattern) {
                    $arguments[] = "(?<{$name}>{$pattern})";
                }

                $aliases = implode('|', $command->getAliases());
                $commandPattern = "(?<{$command->getName()}>{$aliases})";

                if (!empty($arguments)) {
                    $commandPattern .= '\s+' . implode('\s+', $arguments);
                }
                $commandsPattern[] = '(' . $commandPattern . ')';
            }

            $this->pattern = '/\s+' . implode('|', $commandsPattern) . '/iuJ';
        }

        $rc = new ResponseCollection();
        foreach ($collection as $event) {
            /** @var NewMessage $event */
            $message = $event->getItem();

            if (preg_match($this->pattern, $message->text, $matches)) {
                foreach ($this->commands as $command) {
                    if (empty($matches[$command->getName()])) {
                        continue;
                    }

                    $argumentNames = array_keys($command->getArguments());
                    $arguments = array_filter(
                            $matches,
                            fn (string $name): bool => in_array($name, $argumentNames),
                            ARRAY_FILTER_USE_KEY
                    );

                    $r = $command->process($message, $arguments);
                    if (!empty($r)) {
                        $rc->push($r);
                    }
                }
            }
        }

        return $rc;
    }
}