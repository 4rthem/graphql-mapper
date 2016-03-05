<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\FieldContainer;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;

interface MappingGuesserInterface
{
    /**
     * @param Field           $field
     * @param FieldContainer  $fieldContainer
     * @param SchemaContainer $schemaContainer
     * @return Guess
     */
    public function guessType(Field $field, FieldContainer $fieldContainer, SchemaContainer $schemaContainer);

    /**
     * @param Field           $field
     * @param FieldContainer  $fieldContainer
     * @param SchemaContainer $schemaContainer
     * @return Guess
     */
    //public function guessHandler(Field $field, FieldContainer $fieldContainer, SchemaContainer $schemaContainer);
}
