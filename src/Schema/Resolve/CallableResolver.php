<?php

namespace Arthem\GraphQLMapper\Schema\Resolve;

use Arthem\GraphQLMapper\Mapping\Field;
use GraphQL\Type\Definition\ResolveInfo;

class CallableResolver extends SingletonResolver
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'callable';
    }

    /**
     * {@inheritdoc}
     */
    protected function createFunction(array $config, Field $field)
    {
        return function ($node, array $arguments, ResolveInfo $info) {
            $resolveConfig = $this->getResolveConfig($info);

            $function = $resolveConfig['function'];

            return call_user_func_array($function, $arguments);
        };
    }
}
