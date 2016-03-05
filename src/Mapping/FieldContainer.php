<?php

namespace Arthem\GraphQLMapper\Mapping;

abstract class FieldContainer extends AbstractType
{
    /**
     * @var Field[]
     */
    private $fields = [];

    /**
     * The GraphQL class used to build the final schema
     *
     * @var string
     */
    private $internalType = 'ObjectType';

    /**
     * @return Field[]
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param Field[] $fields
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
    public function getInternalType()
    {
        return $this->internalType;
    }

    /**
     * @param string $internalType
     * @return $this
     */
    public function setInternalType($internalType)
    {
        $this->internalType = $internalType;

        return $this;
    }

    /**
     * @return array
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
