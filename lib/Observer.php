<?php


namespace Freezemage\Pizdyk;

use Freezemage\Pizdyk\Vk\Message\Collection;


interface Observer
{
    public function update(Collection $collection): Collection;
}