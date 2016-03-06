<?php

namespace Arthem\GraphQLMapper\Mapping\Context;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\FieldContainer;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;

class FieldContext extends ContainerContext
{
    /**
     * @var Field
     */
    private $field;

    /**
     * @param Field           $field
     * @param FieldContainer  $container
     * @param SchemaContainer $schema
     */
    public function __construct(Field $field, FieldContainer $container, SchemaContainer $schema)
    {
        $this->field     = $field;
        parent::__construct($container, $schema);
    }

    /**
     * @return Field
     */
    public function getField()
    {
        return $this->field;
    }
}
