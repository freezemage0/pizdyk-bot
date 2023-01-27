<?php


namespace Freezemage\Pizdyk\Utility;

class Random
{
    public static function pick(array $collection): mixed
    {
        $index = array_rand($collection);
        return $collection[$index];
    }
}