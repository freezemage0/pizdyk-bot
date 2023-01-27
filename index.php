<?php


use Freezemage\Pizdyk\Censorship\AreaOfEffect\Tracker;
use Freezemage\Pizdyk\Censorship\Observer;
use Freezemage\Pizdyk\Command\Dump;
use Freezemage\Pizdyk\Command\ForcedCensorship;
use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Engine;
use Freezemage\Pizdyk\Output\Responder;
use Freezemage\Pizdyk\Vk\Client as VkClient;
use Freezemage\Pizdyk\Vk\LongPoll\Client as LongPollClient;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Factory as EventFactory;
use Freezemage\Pizdyk\Vk\LongPoll\Factory\Group;
use Freezemage\Pizdyk\Vk\LongPoll\Listener;
use Freezemage\Pizdyk\Vk\Message\Service as MessageService;
use Freezemage\Pizdyk\Vk\User\Service as UserService;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\HttpFactory;


require __DIR__ . '/vendor/autoload.php';


$configuration = new Configuration(__DIR__ . '/config.json');

$credentials = $configuration->getCredentials();

$client = new Client(['verify' => false]);
$factory = new HttpFactory();
$vkClient = new VkClient($client, $factory, $factory, $factory);

$api = $configuration->getApi();

$vkClient->setAuthToken($credentials->communityToken);
$vkClient->setBaseUri($api->baseUri);
$vkClient->setVersion($api->version);

$server = new Listener(
        $configuration,
        new Group($vkClient, $credentials->communityId),
        new LongPollClient($client, $factory, $factory, new EventFactory())
);

$engine = new Engine($server, new Responder(new MessageService($vkClient), new UserService($vkClient), Engine::DELAY));
$engine->attach(new Observer(
        $configuration,
        new Tracker($configuration->getAreaOfEffect())
));
//$engine->attach(new BerserkObserver());

$assets = $configuration->getAssets();
$command = new \Freezemage\Pizdyk\Command\Observer(
        $configuration->getPrefixes(),
        [
                new ForcedCensorship($assets->photos, $assets->audios),
                new Dump($configuration)
        ]
);

$engine->attach($command);
$engine->run();