<?php

namespace Arthem\GraphQLMapper\Schema\Resolve;

use Arthem\GraphQLMapper\Mapping\Field;

interface ResolverInterface
{
    /**
     * @param array $config
     * @param Field $field
     * @return \Closure
     */
    public function getFunction(array $config, Field $field);

    /**
     * @return string
     */
    public function getName();
}
