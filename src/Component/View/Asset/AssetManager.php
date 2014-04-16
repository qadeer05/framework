<?php

namespace Pagekit\Component\View\Asset;

use Pagekit\Component\Routing\UrlGenerator;

class AssetManager implements \IteratorAggregate
{
    /**
     * @var UrlGenerator
     */
    protected $url;

    /**
     * @var string
     */
    protected $version;

    /**
     * @var AssetCollection
     */
    protected $registered;

    /**
     * @var array
     */
    protected $queued = array();

    /**
     * Constructor.
     *
     * @param UrlGenerator $url
     * @param string       $version
     */
    public function __construct(UrlGenerator $url, $version = null)
    {
        $this->url = $url;
        $this->version = $version;
        $this->registered = new AssetCollection;
    }

    /**
     * Create a asset instance.
     *
     * @param  string $name
     * @param  mixed $asset
     * @param  array $dependencies
     * @param  array $options
     * @throws \InvalidArgumentException
     * @return AssetInterface
     */
    protected function create($name, $asset, $dependencies = array(), $options = array())
    {
        if (is_string($options)) {
            $options = array('type' => $options);
        }

        if (!isset($options['type'])) {
            $options['type'] = 'file';
        }

        if ($dependencies) {
            $options = array_merge($options, array('dependencies' => (array) $dependencies));
        }

        if ('string' == $options['type']) {
            return new StringAsset($name, $asset, $options);
        }

        if ('file' == $options['type']) {

            if (isset($options['version'])) {
                $ver = $options['version'];
            } elseif ($this->version) {
                $ver = $options['version'] = $this->version;
            } else {
                $ver = null;
            }

            if (!$this->isAbsolutePath($asset) and strpos($asset, '://') === false) {
                $asset = "asset://$asset";
            }

            $options['path'] = $asset;

            return new FileAsset($name, $this->url->to($options['path'], $ver ? compact('ver') : array()), $options);
        }

        throw new \InvalidArgumentException('Unable to determine asset type.');
    }

    /**
     * Registers an asset.
     *
     * @param  string $name
     * @param  mixed  $asset
     * @param  array  $dependencies
     * @param  array  $options
     * @return self
     */
    public function register($name, $asset, $dependencies = array(), $options = array())
    {
        $this->registered->add($this->create($name, $asset, $dependencies, $options));

        return $this;
    }

    /**
     * Unregisters an asset.
     *
     * @param  string $name
     * @return self
     */
    public function unregister($name)
    {
        $this->registered->remove($name);
        $this->dequeue($name);

        return $this;
    }

    /**
     * Queues a previously registered asset or a new asset.
     *
     * @param  string $name
     * @param  mixed  $asset
     * @param  array  $dependencies
     * @param  array  $options
     * @return self
     */
    public function queue($name, $asset = null, $dependencies = array(), $options = array())
    {
        if (!$instance = $this->registered->get($name)) {
            $this->registered->add($instance = $this->create($name, $asset, $dependencies, $options));
        }

        $this->queued[$instance->getName()] = true;

        return $this;
    }

    /**
     * Dequeues an asset.
     *
     * @param  string $name
     * @return self
     */
    public function dequeue($name)
    {
        unset($this->queued[$name]);

        return $this;
    }

    /**
     * IteratorAggregate interface implementation.
     */
    public function getIterator()
    {
        $assets = array();

        foreach (array_keys($this->queued) as $name) {
            $this->resolveDependencies($this->registered->get($name), $assets);
        }

        return new \ArrayIterator($assets);
    }

    /**
     * Resolve asset dependencies.
     *
     * @param Asset $asset
     * @param array $resolved
     * @param array $unresolved
     * @throws \RuntimeException
     */
    protected function resolveDependencies($asset, &$resolved, &$unresolved = array())
    {
        $unresolved[$asset->getName()] = $asset;

        if (isset($asset['dependencies'])) {
            foreach ($asset['dependencies'] as $dependency) {
                if (!isset($resolved[$dependency])) {

                    if (!$this->registered->get($dependency)) {
                        throw new \RuntimeException(sprintf('Asset dependency "%s" does not exists.', $dependency));
                    }

                    if (isset($unresolved[$dependency])) {
                        throw new \RuntimeException(sprintf('Circular asset dependency "%s > %s" detected.', $asset->getName(), $dependency));
                    }

                    $this->resolveDependencies($this->registered->get($dependency), $resolved, $unresolved);
                }
            }
        }

        $resolved[$asset->getName()] = $asset;
        unset($unresolved[$asset->getName()]);
    }

    /**
     * Returns whether the file path is an absolute path.
     *
     * @param  string $file
     * @return bool
     */
    protected function isAbsolutePath($file)
    {
        return $file && ($file[0] == '/' || $file[0] == '\\' || (strlen($file) > 3 && ctype_alpha($file[0]) && $file[1] == ':' && ($file[2] == '\\' || $file[2] == '/')));
    }
}