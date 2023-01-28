<?php


namespace Freezemage\Pizdyk;

use DomainException;
use Freezemage\Pizdyk\Configuration\Api;
use Freezemage\Pizdyk\Configuration\AreaOfEffect;
use Freezemage\Pizdyk\Configuration\Assets;
use Freezemage\Pizdyk\Configuration\Assets\Audios;
use Freezemage\Pizdyk\Configuration\Assets\Photos;
use Freezemage\Pizdyk\Configuration\Blacklist;
use Freezemage\Pizdyk\Configuration\Credentials;
use Freezemage\Pizdyk\Configuration\Force;
use Freezemage\Pizdyk\Configuration\Ultimate;
use JsonException;
use RuntimeException;


final class Configuration
{
    private string $path;
    private array $data;

    public function __construct(string $path)
    {
        if (pathinfo($path, PATHINFO_EXTENSION) != 'json') {
            throw new DomainException('Configuration MUST be in json format.');
        }

        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException('Configuration file is not readable.');
        }

        $this->path = $path;
    }

    /**
     * @throws JsonException
     */
    private function load(): void
    {
        if (!isset($this->data)) {
            $contents = file_get_contents($this->path);
            $this->data = json_decode($contents, true, 512, JSON_THROW_ON_ERROR);
        }
    }

    private function get(string $key): mixed
    {
        $this->load();
        return $this->data[$key];
    }

    public function getRuleset(): array
    {
        return $this->get('ruleset');
    }

    public function getIgnoredUsers(): array
    {
        return $this->get('ignoredUsers');
    }

    public function getCredentials(): Credentials
    {
        $credentials = $this->get('credentials');

        return new Credentials($credentials['communityId'], $credentials['communityToken']);
    }

    public function getApi(): Api
    {
        $api = $this->get('api');
        return new Api($api['baseUri'], $api['version'], $api['maxLongPollAttempts']);
    }

    public function getAssets(): Assets
    {
        $assets = $this->get('assets');

        $photos = new Photos(
                $assets['photos']['generic'],
                $assets['photos']['aoe'],
                $assets['photos']['forcedCensorship']
        );
        $audios = new Audios($assets['audios']['forcedCensorship']);

        return new Assets($photos, $audios);
    }

    public function getAreaOfEffect(): AreaOfEffect
    {
        $aoe = $this->get('modes');

        return new AreaOfEffect($aoe['aoe']['options']['count'], $aoe['aoe']['options']['period']);
    }

    public function getPrefixes(): array
    {
        return $this->get('prefixes');
    }

    public function getDatabasePath(): string
    {
        return $this->get('dbpath');
    }

    /**
     * @return Ultimate[]
     */
    public function getUltimates(): array
    {
        $modes = $this->get('modes');
        return array_map(
                fn (array $ult): Ultimate => new Ultimate($ult['userId'], $ult['description'], $ult['assets']),
                $modes['ultimate']['options']['ultimates']
        );
    }

    public function getForce(): Force
    {
        $modes = $this->get('modes');
        return new Force($modes['force']['options']['canBeUsedBy']);
    }

    public function getBlacklist(): Blacklist
    {
        $modes = $this->get('modes');
        return new Blacklist($modes['blacklist']['options']['canBeUsedBy']);
    }
}