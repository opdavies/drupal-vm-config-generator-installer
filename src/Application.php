<?php

namespace DrupalVmConfigGenerator\Installer;

use DrupalVmConfigGenerator\Installer\Controller\Installer;
use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient as CachedGithubClient;
use GuzzleHttp\Client as GuzzleClient;
use Igorw\Silex\ConfigServiceProvider;
use Silex\Application as SilexApplication;
use Silex\Provider\ServiceControllerServiceProvider;

class Application extends SilexApplication
{
    public function __construct()
    {
        parent::__construct();

        $this->registerProviders($this);
        $this->registerServices($this);
        $this->createRoutes($this);
    }

    /**
     * @param Application $app
     */
    private function registerProviders(Application $app)
    {
        $app->register(new ServiceControllerServiceProvider());

        $app->register(new ConfigServiceProvider(__DIR__ . '/../config/settings.yml'));
    }

    /**
     * @param Application $app
     */
    private function registerServices(Application $app)
    {
        $app['github.client'] = $app->share(function () use ($app) {
            return new GithubClient(
                new CachedGithubClient(['cache_dir' => $app['github.cache_dir']])
            );
        });

        $app['guzzle'] = $app->share(function () {
            return new GuzzleClient();
        });

        $app['drupalvm.installer'] = $app->share(function () use ($app) {
            return new Installer(
                $app['github.client'],
                $app['guzzle'],
                $app['drupalvm.organisation'],
                $app['drupalvm.repo'],
                $app['drupalvm.phar_name']
            );
        });
    }

    /**
     * @param Application $app
     */
    private function createRoutes(Application $app)
    {
        $app->get('/', function () use ($app) {
            $response = $app['drupalvm.installer']->download();

            return $response;
        });
    }
}
