<?php

namespace Arthem\GraphQLMapper\Mapping;

use GraphQL\Type\Definition\Type as GQLType;

class Field
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var string|GQLType
     */
    private $type;

    /**
     * @var string|callable
     */
    private $map;

    /**
     * @var string|callable
     */
    private $resolve;

    /**
     * @var Field[]
     */
    private $arguments = [];

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return string|GQLType
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string|GQLType $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return string|callable
     */
    public function getMap()
    {
        return $this->map;
    }

    /**
     * @param string|callable $map
     * @return $this
     */
    public function setMap($map)
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @return string|callable
     */
    public function getResolve()
    {
        return $this->resolve;
    }

    /**
     * @param string|callable $resolve
     * @return $this
     */
    public function setResolve($resolve)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * @return Field[]
     */
    public function getArguments()
    {
        return $this->arguments;
    }

    /**
     * @param Field[] $arguments
     * @return $this
     */
    public function setArguments(array $arguments)
    {
        $this->arguments = $arguments;

        return $this;
    }

    /**
     * @return array
     */
    public function toMapping()
    {
        $mapping = [
            'name'        => $this->name,
            'description' => $this->description,
            'type'        => $this->type,
        ];

        if (!empty($this->arguments)) {
            $mapping['args'] = [];
            foreach ($this->arguments as $argument) {
                $mapping['args'][$argument->getName()] = $argument->toMapping();
            }
        }

        if (null !== $this->resolve) {
            $mapping['resolve'] = $this->resolve;
        }

        if (null !== $this->map) {
            $mapping['map'] = $this->map;
        }

        return $mapping;
    }
}
