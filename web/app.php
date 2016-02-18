<?php

require __DIR__ . '/../vendor/autoload.php';

use DrupalVmConfigGenerator\Installer;
use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient as CachedGithubClient;
use Guzzle\Http\Client as GuzzleClient;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

$app = new Application();

$app->register(new ServiceControllerServiceProvider());

$app['drupalvm.organisation'] = 'opdavies';
$app['drupalvm.repo'] = 'drupal-vm-config-generator';

$app['github.cache_dir'] = '/tmp/github-api-cache';

$app['github.client'] = $app->share(function() use ($app) {
    return new GithubClient(
        new CachedGithubClient(['cache_dir' => $app['github.cache_dir']])
    );
});

$app['guzzle'] = $app->share(function() {
    return new GuzzleClient();
});

$app['drupalvm.installer'] = $app->share(function() use ($app) {
    return new Installer(
        $app['github.client'],
        $app['guzzle'],
        $app['drupalvm.organisation'],
        $app['drupalvm.repo']
    );
});

$app->get('/', function() use ($app) {
    return $app['drupalvm.installer']->download();
});

$app->run();
