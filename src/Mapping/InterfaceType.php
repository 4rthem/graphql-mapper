<?php

namespace Arthem\GraphQLMapper\Mapping;

class InterfaceType
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
     * @var Field[]
     */
    private $fields;

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
            $fieldsMapping[] = $field->toMapping();
        }

        return [
            'name'        => $this->name,
            'description' => $this->description,
            'fields'      => $fieldsMapping,
        ];
    }
}
