<?php

namespace Arthem\GraphQLMapper\Factory\Resolver;

use Arthem\GraphQLMapper\Mapping\Field;

interface ResolverFactoryInterface
{
    /**
     * @param Field $field
     * @return callable|null
     */
    public function createResolver(Field $field);
}
