<?php
namespace Arthem\GraphQLMapper\Mapping;

class QuerySchema
{
    /**
     * @var string
     */
    private $description = 'The query root of this schema';

    /**
     * @var Field[]
     */
    private $fields;

    /**
     * @return string
     */
    public function getName()
    {
        return 'Query';
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
        return [
            'name'        => $this->getName(),
            'description' => $this->getDescription(),
            'fields'      => $this->getFieldsMapping(),
        ];
    }

    /**
     * @return Field[]
     */
    private function getFieldsMapping()
    {
        $fieldsMapping = [];

        foreach ($this->fields as $field) {
            $fieldsMapping[$field->getName()] = $field->toMapping();
        }

        return $fieldsMapping;
    }

}
