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
     * @var QuerySchema
     */
    private $querySchema;

    // TODO
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
     * @return InterfaceType[]
     */
    public function getInterfaces()
    {
        return $this->interfaces;
    }

    /**
     * @return QuerySchema
     */
    public function getQuerySchema()
    {
        return $this->querySchema;
    }

    /**
     * @param QuerySchema $querySchema
     * @return $this
     */
    public function setQuerySchema(QuerySchema $querySchema)
    {
        $this->querySchema = $querySchema;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMutationSchema()
    {
        return $this->mutationSchema;
    }

    /**
     * @param mixed $mutationSchema
     * @return $this
     */
    public function setMutationSchema($mutationSchema)
    {
        $this->mutationSchema = $mutationSchema;

        return $this;
    }
}
