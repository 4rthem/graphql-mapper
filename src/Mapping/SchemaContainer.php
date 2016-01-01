<?php
namespace Arthem\GraphQLMapper\Mapping;

class SchemaContainer
{
    /**
     * @var Type[]
     */
    private $types = [];

    /**
     * @var InterfaceType[]
     */
    private $interfaces = [];

    /**
     * @var Query
     */
    private $querySchema;

    /**
     * @var Query
     */
    private $mutationSchema;

    /**
     * @param Type $type
     * @return $this
     */
    public function addType(Type $type)
    {
        $this->types[$type->getName()] = $type;

        return $this;
    }

    /**
     * @param InterfaceType $type
     * @return $this
     */
    public function addInterface(InterfaceType $type)
    {
        $this->interfaces[$type->getName()] = $type;

        return $this;
    }

    /**
     * @return Type[]
     */
    public function getTypes()
    {
        return $this->types;
    }

    /**
     * @return Type
     */
    public function getType($name)
    {
        return $this->types[$name];
    }

    /**
     * @return bool
     */
    public function hasType($name)
    {
        return isset($this->types[$name]);
    }

    /**
     * @return InterfaceType[]
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @return Query
     */
    public function getQuerySchema()
    {
        return $this->querySchema;
    }

    /**
     * @param Query $querySchema
     * @return $this
     */
    public function setQuerySchema(Query $querySchema)
    {
        $this->querySchema = $querySchema;

        return $this;
    }

    /**
     * @return Query
     */
    public function getMutationSchema()
    {
        return $this->mutationSchema;
    }

    /**
     * @param Query $mutationSchema
     * @return $this
     */
    public function setMutationSchema(Query $mutationSchema)
    {
        $this->mutationSchema = $mutationSchema;

        return $this;
    }
}
