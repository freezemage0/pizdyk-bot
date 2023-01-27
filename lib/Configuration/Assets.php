<?php


namespace Freezemage\Pizdyk\Configuration;

use Freezemage\Pizdyk\Configuration\Assets\Audios;
use Freezemage\Pizdyk\Configuration\Assets\Photos;


final class Assets
{
    public Photos $photos;
    public Audios $audios;

    public function __construct(Photos $photos, Audios $audios)
    {
        $this->photos = $photos;
        $this->audios = $audios;
    }
}