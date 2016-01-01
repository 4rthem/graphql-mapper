<?php

namespace Arthem\GraphQLMapper\Mapping;

use GraphQL\Type\Definition\Type as GQLType;

class Field extends AbstractType
{
    /**
     * The model field name
     *
     * @var string
     */
    private $field;

    /**
     * @var string
     */
    private $type;

    /**
     * @var callable|GQLType
     */
    private $resolvedType;

    /**
     * @var callable
     */
    private $resolve;

    /**
     * @var array
     */
    private $resolveConfig = [];

    /**
     * @var Field[]
     */
    private $arguments = [];

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return callable|GQLType
     */
    public function getResolvedType()
    {
        return $this->resolvedType;
    }

    /**
     * @param callable|GQLType $resolvedType
     * @return $this
     */
    public function setResolvedType($resolvedType)
    {
        $this->resolvedType = $resolvedType;

        return $this;
    }

    /**
     * @return callable
     */
    public function getResolve()
    {
        return $this->resolve;
    }

    /**
     * @param callable $resolve
     * @return $this
     */
    public function setResolve($resolve)
    {
        $this->resolve = $resolve;

        return $this;
    }

    /**
     * @return array
     */
    public function getResolveConfig()
    {
        return $this->resolveConfig;
    }

    /**
     * @param array $resolveConfig
     * @return $this
     */
    public function setResolveConfig($resolveConfig)
    {
        $this->resolveConfig = $resolveConfig;

        return $this;
    }

    /**
     * @param array $config
     * @return $this
     */
    public function mergeRevolveConfig(array $config)
    {
        $this->resolveConfig = array_merge($this->resolveConfig, $config);

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
     * @return array<string,mixed>
     */
    public function toMapping()
    {
        $mapping = [
                'type'           => $this->resolvedType,
                'resolve'        => $this->resolve,
                'resolve_config' => $this->resolveConfig,
            ] + parent::toMapping();

        if (!empty($this->arguments)) {
            $mapping['args'] = [];
            foreach ($this->arguments as $argument) {
                $mapping['args'][$argument->getName()] = $argument->toMapping();
            }
        }

        return $mapping;
    }
}
