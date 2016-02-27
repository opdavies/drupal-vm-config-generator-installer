<?php

namespace DrupalVmConfigGenerator\Installer;

use DrupalVmConfigGenerator\Installer\Controller\Installer;
use Github\Client as GithubClient;
use Github\HttpClient\CachedHttpClient as CachedGithubClient;
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

        # Include global settings.
        $app->register(new ConfigServiceProvider(__DIR__ . '/../config/settings.yml'));

        # Include local settings.
        if (file_exists(__DIR__ . '/../config/settings.local.yml')) {
            $app->register(new ConfigServiceProvider(__DIR__ . '/../config/settings.local.yml'));
        }
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

        $app['drupalvm.installer'] = $app->share(function () use ($app) {
            return new Installer(
                $app['github.client'],
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
            return $app['drupalvm.installer']->redirect();
        });
    }
}
