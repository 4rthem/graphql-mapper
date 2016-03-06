<?php

namespace Arthem\GraphQLMapper\Mapping;

abstract class FieldContainer extends AbstractType
{
    /**
     * @var Field[]
     */
    private $fields = [];

    /**
     * The model class
     *
     * @var string
     */
    private $model;

    /**
     * @var array
     */
    private $resolveConfig = [];

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return $this
     */
    public function setFields(array $fields)
    {
        $this->fields = $fields;

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
    public function setResolveConfig(array $resolveConfig)
    {
        $this->resolveConfig = $resolveConfig;

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
     * {@inheritdoc}
     */
    public function toMapping()
    {
        $fieldsMapping = [];
        foreach ($this->fields as $field) {
            $fieldsMapping[$field->getName()] = $field->toMapping();
        }

        return [
            'fields' => $fieldsMapping,
        ] + parent::toMapping();
    }
}
