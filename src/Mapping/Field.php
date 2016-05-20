<?php

namespace Arthem\GraphQLMapper\Mapping;

use GraphQL\Type\Definition\Type as GQLType;

class Field extends AbstractType
{
    /**
     * The model property name if different from the name
     *
     * @var string
     */
    private $property;

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
    public function getProperty()
    {
        return $this->property;
    }

    /**
     * @param string $property
     * @return $this
     */
    public function setProperty($property)
    {
        $this->property = $property;

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
        if (!is_callable($resolvedType) && !$resolvedType instanceof GQLType) {
            throw new \RuntimeException(sprintf('Invalid $resolvedType, got "%s"', gettype($resolvedType)));
        }

        $this->resolvedType = $resolvedType;

        return $this;
    }

    /**
     * @param array $resolveConfig
     * @return $this
     */
    public function mergeResolveConfig(array $resolveConfig)
    {
        $this->resolveConfig = array_merge($resolveConfig, $this->resolveConfig);

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
    public function setResolveConfig(array $resolveConfig)
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
        $this->resolveConfig = array_merge($config, $this->resolveConfig);

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
     * {@inheritdoc}
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
