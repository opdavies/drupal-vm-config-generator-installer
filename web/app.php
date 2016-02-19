<?php

require __DIR__ . '/../bootstrap.php';

$app->get('/', function () use ($app) {
    return $app['drupalvm.installer']->download();
});

$app->run();
