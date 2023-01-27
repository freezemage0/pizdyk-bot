<?php


namespace Freezemage\Pizdyk\Vk\User;

class Item
{
    public int $id;
    public ?string $handle;
    public ?string $firstName;
    public ?string $lastName;

    public function __construct(int $id, ?string $handle, ?string $firstName = null, ?string $lastName = null)
    {
        $this->id = $id;
        $this->handle = $handle;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}