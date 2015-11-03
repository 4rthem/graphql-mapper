<?php

namespace Arthem\GraphQLMapper\Mapping\Driver;

interface FilePathAccessorInterface
{
    /**
     * Returns a collection of file paths
     *
     * @return array
     */
    public function getPaths();
}
