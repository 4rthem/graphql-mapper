<?php

namespace Arthem\GraphQLMapper\Mapping;

class Type extends FieldContainer
{
    /**
     * @var string
     */
    private $extends;

    /**
     * @var array
     */
    private $resolveConfig;

    /**
     * @return string
     */
    public function getExtends()
    {
        return $this->extends;
    }

    /**
     * @param string $extends
     * @return $this
     */
    public function setExtends($extends)
    {
        $this->extends = $extends;

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
     * @return array
     */
    public function toMapping()
    {
        $mapping = parent::toMapping();

        $mapping['extends'] = $this->extends;

        return $mapping;
    }
}
