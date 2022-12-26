<?php


namespace Freezemage\Pizdyk\Filter;

interface Constraint
{
    public function evaluate(Filterable $item): bool;
}