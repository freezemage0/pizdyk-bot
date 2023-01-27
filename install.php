<?php


use Freezemage\Pizdyk\Configuration;


require __DIR__ . '/vendor/autoload.php';

$config = new Configuration(__DIR__ . '/config.json');
$driver = new SQLite3(getcwd() . $config->getDatabasePath());

$driver->query("CREATE TABLE IF NOT EXISTS statistics (peerId INTEGER NOT NULL, name VARCHAR(255), counter INTEGER NOT NULL)");
$driver->query("CREATE TABLE IF NOT EXISTS statistics_top (userId INTEGER NOT NULL, peerId INTEGER NOT NULL, counter INTEGER NOT NULL)");