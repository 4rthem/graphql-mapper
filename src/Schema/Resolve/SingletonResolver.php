<?php

namespace Arthem\GraphQLMapper\Schema\Resolve;

use Arthem\GraphQLMapper\Mapping\Field;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\ResolveInfo;

abstract class SingletonResolver implements ResolverInterface
{
    /**
     * @var \Closure
     */
    protected $callback;

    /**
     * {@inheritdoc}
     */
    public function getFunction(array $config, Field $field)
    {
        if (null !== $this->callback) {
            return $this->callback;
        }

        $this->callback = $this->createFunction($config, $field);

        return $this->callback;
    }

    protected function getResolveConfig(ResolveInfo $info)
    {
        /** @var ObjectType $parentType */
        $parentType = $info->parentType;
        $field      = $parentType->getField($info->fieldName);

        $resolveConfig = $field->config['resolve_config'];

        return $resolveConfig;
    }

    /**
     * @return mixed
     */
    abstract protected function createFunction(array $config, Field $field);
}
