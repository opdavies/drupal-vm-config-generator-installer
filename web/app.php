<?php

use DrupalVmConfigGenerator\Installer\Application;

require_once __DIR__ . '/../vendor/autoload.php';

date_default_timezone_set('Europe/London');

$app = new Application();

$app->run();
