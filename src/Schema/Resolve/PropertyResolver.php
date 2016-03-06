<?php

namespace Arthem\GraphQLMapper\Schema\Resolve;

use Arthem\GraphQLMapper\Mapping\Field;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

class PropertyResolver extends SingletonResolver
{
    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'property';
    }

    /**
     * {@inheritdoc}
     */
    public function createFunction(array $config, Field $field)
    {
        return function ($node, array $arguments, ResolveInfo $info) {
            /** @var ObjectType $parentType */
            $parentType = $info->parentType;
            $field      = $parentType->getField($info->fieldName);

            $resolveConfig = $field->config['resolve_config'];

            return call_user_func([$node, $resolveConfig['method']]);
        };
    }
}
