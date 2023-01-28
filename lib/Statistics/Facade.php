<?php


namespace Freezemage\Pizdyk\Statistics;

use Freezemage\Pizdyk\Statistics\Top\Item as TopItem;
use Freezemage\Pizdyk\Statistics\Top\Repository as TopRepository;


final class Facade
{
    const TOP_SIZE = 10;
    private Repository $repository;
    private TopRepository $topRepository;

    public function __construct(Repository $repository, TopRepository $topRepository)
    {
        $this->repository = $repository;
        $this->topRepository = $topRepository;
    }

    public function track(int $peerId, string $name, int $count = 1): void
    {
        $stat = $this->repository->findByName($peerId, $name);
        if (empty($stat)) {
            $stat = new Item(null, $peerId, $name, $count);
            $this->repository->add($stat);
        } else {
            $stat->counter += $count;
            $this->repository->update($stat);
        }
    }

    public function trackUser(int $peerId, int $userId, int $count = 1): void
    {
        $top = $this->topRepository->findOne($peerId, $userId);
        if (empty($top)) {
            $top = new TopItem($userId, $peerId, $count);
            $this->topRepository->add($top);
        } else {
            $top->counter += $count;
            $this->topRepository->update($top);
        }
    }

    public function get(int $peerId): array
    {
        $stats = $this->repository->find($peerId);
        $stats[] = new Item(
                null,
                $peerId,
                "Общее количество мыслепреступлений",
                $this->topRepository->count($peerId)
        );

        $users = $this->topRepository->find($peerId, Facade::TOP_SIZE);

        return ['global' => $stats, 'users' => $users];
    }
}