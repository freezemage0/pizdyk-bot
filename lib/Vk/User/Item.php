<?php


namespace Freezemage\Pizdyk\Vk\User;

class Item
{
    public int $id;
    public ?string $handle;

    public function __construct(int $id, ?string $handle)
    {
        $this->id = $id;
        $this->handle = $handle;
    }
}