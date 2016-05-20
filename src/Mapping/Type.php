<?php

namespace Arthem\GraphQLMapper\Mapping;

class Type extends FieldContainer
{
    /**
     * @var array
     */
    private $interfaces = [];

    /**
     * @var array
     */
    private $values;

    /**
     * @return array
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @param array $interfaces
     * @return $this
     */
    public function setInterfaces(array $interfaces)
    {
        $this->interfaces = $interfaces;

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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toMapping()
    {
        $mapping = parent::toMapping();

        if ($this->interfaces) {
            $mapping['interfaces'] = $this->interfaces;
        }
        if ($this->values) {
            $mapping['values'] = $this->values;
        }

        return $mapping;
    }
}
