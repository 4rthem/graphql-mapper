<?php

namespace Arthem\GraphQLMapper\Mapping\Cache;

use Arthem\GraphQLMapper\Mapping\SchemaContainer;

interface CacheDriverInterface
{
    /**
     * Loads the whole GraphQL schema
     *
     * @return SchemaContainer|false
     */
    public function load();

    /**
     * Caches the whole GraphQL schema
     *
     * @param SchemaContainer $container
     */
    public function save(SchemaContainer $container);
}
