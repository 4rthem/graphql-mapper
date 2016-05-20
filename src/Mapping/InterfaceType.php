<?php

namespace Arthem\GraphQLMapper\Mapping;

class InterfaceType extends FieldContainer
{
    /**
     * @var callable
     */
    private $resolveType;

    /**
     * @var array
     */
    private $childrenClassMapping = [];

    /**
     * @param callable $resolveType
     * @return $this
     */
    public function setResolveType($resolveType)
    {
        $this->resolveType = $resolveType;

        return $this;
    }

    /**
     * @param string $typeName
     * @param string $className
     * @return $this
     */
    public function setChildClass($typeName, $className)
    {
        $this->childrenClassMapping[$className] = $typeName;

        return $this;
    }

    /**
     * @return array
     */
    public function getChildrenClassMapping()
    {
        return $this->childrenClassMapping;
    }

    /**
     * {@inheritdoc}
     */
    public function toMapping()
    {
        $mapping = parent::toMapping();

        if (null !== $this->resolveType) {
            $mapping['resolveType'] = $this->resolveType;
        }

        return $mapping;
    }
}
