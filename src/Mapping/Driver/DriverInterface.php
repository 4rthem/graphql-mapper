<?php

namespace Arthem\GraphQLMapper\Mapping\Driver;

use Arthem\GraphQLMapper\Mapping\SchemaContainer;

interface DriverInterface
{
    /**
     * @param SchemaContainer $schema
     */
    public function load(SchemaContainer $schema);
}
