<?php

require __DIR__ . '/../vendor/autoload.php';
setlocale(LC_ALL, 'cs_CZ');
$configurator = new Nette\Configurator;

//$configurator->setDebugMode('23.75.345.200'); // enable for your remote IP
$configurator->enableDebugger(__DIR__ . '/../log');

$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/config/config.neon');
$configurator->addConfig(__DIR__ . '/config/config.local.neon');

$container = $configurator->createContainer();

Kdyby\Replicator\Container::register();
MultipleFileUpload\MultipleFileUpload::register();


return $container;
