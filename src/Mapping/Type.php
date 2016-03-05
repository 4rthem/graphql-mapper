<?php

namespace Arthem\GraphQLMapper\Mapping;

class Type extends FieldContainer
{
    /**
     * @var string
     */
    private $extends;

    /**
     * The model class
     *
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $resolveConfig;

    /**
     * @var array
     */
    private $values;

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
     * @return string
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param string $model
     * @return $this
     */
    public function setModel($model)
    {
        $this->model = $model;

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
    public function setResolveConfig(array $resolveConfig = null)
    {
        $this->resolveConfig = $resolveConfig;

        return $this;
    }

    /**
     * @return array
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function setValues(array $values)
    {
        $this->values = $values;
        $this->setInternalType('EnumType');
        $this->resolveConfig = array(
            'handler' => 'enum',
        );

        return $this;
    }

    /**
     * @return array
     */
    public function toMapping()
    {
        $mapping = parent::toMapping();

        $mapping['extends'] = $this->extends;
        $mapping['values'] = $this->values;

        return $mapping;
    }
}
