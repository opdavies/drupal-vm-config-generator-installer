<?php

use DrupalVmConfigGenerator\Installer;
use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient as CachedGithubClient;
use Guzzle\Http\Client as GuzzleClient;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Application;
use Silex\Provider\ServiceControllerServiceProvider;

require __DIR__ . '/vendor/autoload.php';

$app = new Application();

$app->register(new ServiceControllerServiceProvider());

$app->register(new ConfigServiceProvider(__DIR__ . '/config/settings.yml'));

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
        $app['drupalvm.repo'],
        $app['drupalvm.phar_name']
    );
});
