<?php

namespace DrupalVmConfigGenerator;

use Github\Client as GithubClient;
use Guzzle\Http\Client as GuzzleClient;

/**
 * Class Installer
 *
 * @package DrupalVmConfigGenerator
 */
class Installer {

    /**
     * @var GithubClient
     */
    private $github;

    /**
     * @var GuzzleClient
     */
    private $guzzle;

    /**
     * @var string
     */
    private $organisation;

    /**
     * @var string
     */
    private $repository;

    /**
     * @var string
     */
    private $pharName;

    public function __construct(
        GithubClient $github,
        GuzzleClient $guzzle,
        $organisation,
        $repository,
        $pharName
    ) {
        $this->github = $github;
        $this->guzzle = $guzzle;
        $this->organisation = $organisation;
        $this->repository = $repository;
        $this->pharName = $pharName;
    }

    public function download() {
        $filename = sprintf(
            'https://github.com/%s/%s/releases/download/%s/%s',
            $this->organisation,
            $this->repository,
            $this->getLatestRelease(),
            $this->pharName
        );

        return $this->guzzle->get($filename);
    }

    /**
     * Get the latest release tag from GitHub.
     *
     * @return string
     */
    private function getLatestRelease()
    {
        $release = $this->github->api('repo')->releases()->latest(
            $this->organisation,
            $this->repository
        );

        return $release['tag_name'];
    }
}
