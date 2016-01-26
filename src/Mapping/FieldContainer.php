<?php

namespace Arthem\GraphQLMapper\Mapping;

abstract class FieldContainer extends AbstractType
{
    /**
     * @var Field[]
     */
    private $fields = [];

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
