<?php

namespace Arthem\GraphQLMapper\Factory\Mapper;

use Arthem\GraphQLMapper\Mapping\Field;
use GraphQL\Type\Definition\ResolveInfo;
use GraphQL\Type\Definition\Type;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DefaultMapperFactory implements MapperFactoryInterface
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param PropertyAccessor|null $propertyAccessor
     */
    public function __construct(PropertyAccessor $propertyAccessor = null)
    {
        if (null === $propertyAccessor) {
            $propertyAccessor = PropertyAccess::createPropertyAccessor();
        }
        $this->propertyAccessor = $propertyAccessor;
    }

    /**
     * @inheritdoc
     */
    public function createMapper(Field $field)
    {
        $fieldName = $field->getName();
        $typeName  = $field->getType();

        if (in_array($typeName, [
            Type::ID,
            Type::STRING,
            Type::BOOLEAN,
            Type::INT,
            Type::FLOAT,
        ])) {
            return function ($listOfValues, array  $args, ResolveInfo $info) use ($fieldName) {
                return array_map(function ($object) use ($fieldName) {
                    return $this->propertyAccessor->getValue($object, $fieldName);
                }, $listOfValues);
            };
        }
    }
}
