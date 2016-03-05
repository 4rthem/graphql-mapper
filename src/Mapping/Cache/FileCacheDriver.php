<?php

namespace Arthem\GraphQLMapper\Mapping\Cache;

use Arthem\GraphQLMapper\Mapping\SchemaContainer;

class FileCacheDriver implements CacheDriverInterface
{
    /**
     * @var string
     */
    private $cacheFile;

    /**
     * @var bool
     */
    private $cacheEnabled;

    /**
     * @param string $cacheFile
     * @param bool   $cacheEnabled
     */
    public function __construct($cacheFile, $cacheEnabled = true)
    {
        $this->cacheFile    = $cacheFile;
        $this->cacheEnabled = $cacheEnabled;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        if (!$this->cacheEnabled || !is_file($this->cacheFile)) {
            return false;
        }

        return unserialize(file_get_contents($this->cacheFile));
    }

    /**
     * {@inheritdoc}
     */
    public function save(SchemaContainer $container)
    {
        if (!$this->cacheEnabled) {
            return;
        }

        file_put_contents($this->cacheFile, serialize($container));
    }

}
