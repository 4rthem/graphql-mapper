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
     * @var Mutation
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
     * @param string $name
     * @return Type
     */
    public function getType($name)
    {
        return $this->types[$name];
    }

    /**
     * @param string $name
     * @return InterfaceType
     */
    public function getInterface($name)
    {
        return $this->interfaces[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasType($name)
    {
        return isset($this->types[$name]);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasInterface($name)
    {
        return isset($this->interfaces[$name]);
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
     * @return Mutation
     */
    public function getMutationSchema()
    {
        return $this->mutationSchema;
    }

    /**
     * @param Mutation $mutationSchema
     * @return $this
     */
    public function setMutationSchema(Mutation $mutationSchema)
    {
        $this->mutationSchema = $mutationSchema;

        return $this;
    }
}
