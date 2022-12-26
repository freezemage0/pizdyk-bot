<?php


use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Engine;
use Freezemage\Pizdyk\Observer\Censorship;
use Freezemage\Pizdyk\Vk\Client as VkClient;
use Freezemage\Pizdyk\Vk\LongPoll\Client as LongPollClient;
use Freezemage\Pizdyk\Vk\LongPoll\Factory\Group;
use Freezemage\Pizdyk\Vk\LongPoll\Listener;
use Freezemage\Pizdyk\Vk\Message\Service;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;


require __DIR__ . '/vendor/autoload.php';


$configuration = new Configuration(
        __DIR__ . '/config.json'
);

$credentials = $configuration->getCredentials();

$client = new Client(['verify' => false]);
$factory = new HttpFactory();
$vkClient = new VkClient($client, $factory, $factory, $factory);

$api = $configuration->getApi();

$vkClient->setAuthToken($credentials->communityToken);
$vkClient->setBaseUri($api->getBaseUri());
$vkClient->setVersion($api->getVersion());

$server = new Listener(
        $configuration,
        new Group($vkClient, $credentials->communityId),
        new LongPollClient($client, $factory, $factory)
);

$engine = new Engine($configuration, $server, new Service($vkClient));
$engine->attach(new Censorship($configuration));
$engine->run();