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
     * @param string $cacheFile
     */
    public function __construct($cacheFile)
    {
        $this->cacheFile = $cacheFile;
    }

    /**
     * {@inheritdoc}
     */
    public function load()
    {
        if (!is_file($this->cacheFile)) {
            return false;
        }

        return unserialize(file_get_contents($this->cacheFile));
    }

    /**
     * {@inheritdoc}
     */
    public function save(SchemaContainer $container)
    {
        file_put_contents($this->cacheFile, serialize($container));
    }

}
