<?php


namespace Freezemage\Pizdyk\Observer;

use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Observer;
use Freezemage\Pizdyk\Observer\Constraint\RemoveIgnoredUsers;
use Freezemage\Pizdyk\Vk\Message\Collection;
use Freezemage\Pizdyk\Vk\Message\Item;


final class Censorship implements Observer
{
    private Configuration $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function update(Collection $collection): Collection
    {
        $collection = $collection->filter(new RemoveIgnoredUsers($this->configuration->getIgnoredUsers()));

        $rules = implode('|', $this->configuration->getRuleset());

        $responses = new Collection();
        foreach ($collection as $item) {
            if (!isset($item->text)) {
                continue;
            }

            if (!preg_match_all("/\S*({$rules})\S*/iu", $item->text, $matches)) {
                continue;
            }

            $matches = array_map(fn (string $match): string => "> {$match}", $matches[0]);
            array_unshift($matches, "@id{$item->senderId}\n");
            $message = implode("\n", $matches);

            $responses->push(new Item($item->peerId, null, $message, $this->getRandomGenericAsset()));
        }

        return $responses;
    }

    private function getRandomGenericAsset(): array
    {
        $assets = $this->configuration->getAssets();
        $generic = $assets->getGeneric();

        $asset = $generic[array_rand($generic)];
        if (!is_array($asset)) {
            $asset = array($asset);
        }
        return $asset;
    }
}