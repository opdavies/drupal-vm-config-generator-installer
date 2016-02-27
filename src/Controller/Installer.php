<?php

namespace DrupalVmConfigGenerator\Installer\Controller;

use Github\Client as GithubClient;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Class Installer
 *
 * @package DrupalVmConfigGenerator
 */
class Installer
{
    /**
     * @var GithubClient
     */
    private $github;

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
        $organisation,
        $repository,
        $pharName
    ) {
        $this->github = $github;
        $this->organisation = $organisation;
        $this->repository = $repository;
        $this->pharName = $pharName;
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

    /**
     * Build the GitHub URL to the file to download.
     *
     * @return string
     */
    private function getFilename()
    {
        $filename = sprintf(
            'https://github.com/%s/%s/releases/download/%s/%s',
            $this->organisation,
            $this->repository,
            $this->getLatestRelease(),
            $this->pharName
        );

        return $filename;
    }

    /**
     * @return RedirectResponse
     */
    public function redirect()
    {
        return new RedirectResponse($this->getFilename());
    }
}
