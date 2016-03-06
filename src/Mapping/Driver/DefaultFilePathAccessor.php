<?php

namespace Arthem\GraphQLMapper\Mapping\Driver;

class DefaultFilePathAccessor implements FilePathAccessorInterface
{
    /**
     * @var array
     */
    private $paths;

    /**
     * @param array $paths
     */
    public function __construct(array $paths)
    {
        $this->paths = $paths;
    }

    /**
     * {@inheritdoc}
     */
    public function getPaths()
    {
        return $this->paths;
    }
}
