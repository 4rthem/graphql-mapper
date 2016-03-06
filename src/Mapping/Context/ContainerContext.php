<?php

namespace Arthem\GraphQLMapper\Mapping\Context;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\FieldContainer;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;

class ContainerContext
{
    /**
     * @var FieldContainer
     */
    private $container;

    /**
     * @var SchemaContainer
     */
    private $schema;

    /**
     * @param FieldContainer  $container
     * @param SchemaContainer $schema
     */
    public function __construct(FieldContainer $container, SchemaContainer $schema)
    {
        $this->container = $container;
        $this->schema    = $schema;
    }

    /**
     * @return FieldContainer
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return SchemaContainer
     */
    public function getSchema()
    {
        return $this->schema;
    }

    /**
     * @param Field $field
     * @return FieldContext
     */
    public function createFieldContext(Field $field)
    {
        return new FieldContext($field, $this->container, $this->schema);
    }
}
