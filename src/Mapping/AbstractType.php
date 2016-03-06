<?php

namespace Arthem\GraphQLMapper\Mapping;

abstract class AbstractType
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
     * The GraphQL class used to build the final schema
     *
     * @var string
     */
    private $internalType = 'ObjectType';

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
        return [
            'name'        => $this->name,
            'description' => $this->description,
        ];
    }
}
