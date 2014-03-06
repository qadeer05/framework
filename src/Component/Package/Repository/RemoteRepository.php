<?php

namespace Pagekit\Component\Package\Repository;

use Pagekit\Component\Http\Client;
use Pagekit\Component\Package\Exception\BadMethodCallException;
use Pagekit\Component\Package\Loader\ArrayLoader;
use Pagekit\Component\Package\Loader\LoaderInterface;
use Pagekit\Component\Package\PackageInterface;

class RemoteRepository implements RepositoryInterface
{
    /**
     * @var string
     */
    protected $url;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * @var Client
     */
    protected $client;

    /**
     * Constructor.
     */
    public function __construct($url, LoaderInterface $loader = null, Client $client = null)
    {
        $this->url    = rtrim($url, '/');
        $this->loader = $loader ?: new ArrayLoader;
        $this->client = $client ?: new Client;
    }

    /**
     * {@inheritdoc}
     */
    public function findPackage($name, $version = 'latest')
    {
        // normalize name
        $name = strtolower($name);

        try {
            if ($package = json_decode($this->client->get("{$this->url}/{$name}/{$version}")->send()->getBody(), true)) {
                return $this->loader->load($package);
            }
        } catch (\Exception $e) {}
    }

    /**
     * {@inheritdoc}
     */
    public function findPackages($name, $version = null)
    {
        // normalize name
        $name = strtolower($name);

        if ($version !== null) {
            return array($this->findPackage($name, $version));
        }

        $packages = array();

        if ($packageInfo = json_decode($this->client->get("{$this->url}/{$name}")->send()->getBody(), true) and isset($packageInfo['versions'])) {
            foreach ($packageInfo['versions'] as $version) {
                $packages[] = $this->loader->load($version);
            }
        }

        return $packages;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPackage(PackageInterface $package)
    {
        return (bool) $this->findPackage($package->getName(), $package->getVersion());
    }

    /**
     * {@inheritdoc}
     */
    public function filterPackages($callback, $class = 'Pagekit\Component\Package\Package')
    {
        throw new BadMethodCallException('RemoteRepository does not support "filterPackages" function');
    }

    /**
     * {@inheritdoc}
     */
    public function getPackages()
    {
        throw new BadMethodCallException('RemoteRepository does not support "getPackages" function');
    }

    /**
     * {@inheritdoc}
     */
    public function addPackage(PackageInterface $package)
    {
        throw new BadMethodCallException('RemoteRepository does not support "addPackage" function');
    }

    /**
     * {@inheritdoc}
     */
    public function removePackage(PackageInterface $package)
    {
        throw new BadMethodCallException('RemoteRepository does not support "removePackage" function');
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return 1;
    }
}
