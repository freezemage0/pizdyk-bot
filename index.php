<?php


use Freezemage\Pizdyk\Censorship\AreaOfEffect\Tracker;
use Freezemage\Pizdyk\Censorship\Observer;
use Freezemage\Pizdyk\Command\ForcedCensorship;
use Freezemage\Pizdyk\Command\Help;
use Freezemage\Pizdyk\Command\Observer as CommandObserver;
use Freezemage\Pizdyk\Command\Statistics;
use Freezemage\Pizdyk\Command\UltimateCensorship;
use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Engine;
use Freezemage\Pizdyk\Output\Responder;
use Freezemage\Pizdyk\Statistics\Facade as StatisticsFacade;
use Freezemage\Pizdyk\Statistics\Repository as StatisticsRepository;
use Freezemage\Pizdyk\Statistics\Top\Repository as StatisticsTopRepository;
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
$driver = new SQLite3(getcwd() . $configuration->getDatabasePath());
$statisticsFacade = new StatisticsFacade(new StatisticsRepository($driver), new StatisticsTopRepository($driver));

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

$userService = new UserService($vkClient);

$engine = new Engine($server, new Responder(new MessageService($vkClient), $userService, Engine::DELAY));
$engine->attach(new Observer(
        $configuration,
        $statisticsFacade,
        new Tracker($configuration->getAreaOfEffect())
));
//$engine->attach(new BerserkObserver());

$assets = $configuration->getAssets();
$commands = [
        new ForcedCensorship($assets->photos, $assets->audios, $configuration->getForce(), $statisticsFacade),
        new Statistics($statisticsFacade, $userService),
        new UltimateCensorship($statisticsFacade, $configuration)
];

$help = new Help($configuration->getPrefixes(), $commands);
$commands[] = $help;

$command = new CommandObserver($configuration->getPrefixes(), $commands);

$engine->attach($command);
$engine->run();