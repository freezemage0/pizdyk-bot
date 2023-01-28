<?php


namespace Freezemage\Pizdyk\Censorship;

use Freezemage\Pizdyk\Censorship\AreaOfEffect\Tracker;
use Freezemage\Pizdyk\Censorship\Constraint\IsIncoming;
use Freezemage\Pizdyk\Censorship\Constraint\IsNotIgnoredUser;
use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\ObserverInterface;
use Freezemage\Pizdyk\Output\ReplyTarget\All;
use Freezemage\Pizdyk\Output\ReplyTarget\User;
use Freezemage\Pizdyk\Output\Response;
use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Statistics\Facade as StatisticsFacade;
use Freezemage\Pizdyk\Utility\Random;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection as EventCollection;
use Freezemage\Pizdyk\Vk\Message\Item;
use RuntimeException;


final class Observer implements ObserverInterface
{
    private Configuration $configuration;
    private Tracker $tracker;
    private StatisticsFacade $facade;

    public function __construct(
            Configuration $configuration,
            StatisticsFacade $facade,
            Tracker $tracker
    ) {
        $this->configuration = $configuration;
        $this->facade = $facade;
        $this->tracker = $tracker;
    }

    public function update(EventCollection $collection): ResponseCollection
    {
        $collection = $collection
                ->filter(new IsIncoming())
                ->filter(new IsNotIgnoredUser($this->configuration->getIgnoredUsers()));

        $rules = implode('|', $this->configuration->getRuleset());

        $responses = new ResponseCollection();
        foreach ($collection as $item) {
            $message = $item->getItem();

            if (!($message instanceof Item)) {
                throw new RuntimeException('Unprocessable item.');
            }

            if (!isset($message->text)) {
                continue;
            }

            if (!preg_match_all("/\S*({$rules})\S*/iu", $message->text, $matches)) {
                continue;
            }

            $matches = array_map(fn (string $match): string => "> {$match}", $matches[0]);

            $responses->push(new Response(
                    $message->peerId,
                    new User($message->senderId),
                    implode("\n", $matches),
                    $this->getRandomGenericAsset()
            ));
            $this->facade->trackUser($message->peerId, $message->senderId);

            $this->tracker->push($message);
        }

        $exceedingPeersCount = [];
        foreach ($this->tracker->getExceedingPeers() as $peer) {
            if (empty($exceedingPeersCount[$peer])) {
                $exceedingPeersCount[$peer] = 0;
            }

            $exceedingPeersCount[$peer] += 1;
            $responses->push(new Response(
                    $peer,
                    new All(),
                    '',
                    $this->configuration->getAssets()->photos->aoe
            ));

            $this->tracker->clear($peer);
        }

        foreach ($exceedingPeersCount as $peerId => $counter) {
            $this->facade->track($peerId, 'Срабатываний АОЕ', $counter);
        }

        return $responses;
    }

    private function getRandomGenericAsset(): array
    {
        $assets = $this->configuration->getAssets();

        $asset = Random::pick($assets->photos->generic);
        if (!is_array($asset)) {
            $asset = [$asset];
        }
        return $asset;
    }
}