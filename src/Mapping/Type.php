<?php

namespace Arthem\GraphQLMapper\Mapping;

class Type
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
     * @var string
     */
    private $extends;

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
    public function toMapping()
    {
        $fieldsMapping = [];

        foreach ($this->fields as $field) {
            $fieldsMapping[$field->getName()] = $field->toMapping();
        }

        $mapping = [
            'name'        => $this->name,
            'description' => $this->description,
            'fields'      => $fieldsMapping,
            'extends'     => $this->extends,
        ];

        if (null !== $this->extends) {
            $mapping['extends'] = $this->extends;
        }

        return $mapping;
    }
}
