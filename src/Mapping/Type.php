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

        $mapping['extends'] = $this->extends;
        $mapping['values']  = $this->values;

        return $mapping;
    }
}
