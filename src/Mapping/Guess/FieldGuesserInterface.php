<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

use Arthem\GraphQLMapper\Mapping\Context\FieldContext;

interface FieldGuesserInterface extends GuesserInterface
{
    /**
     * Guess the GraphQL type of a field
     *
     * @param FieldContext $fieldContext
     * @return TypeGuess
     */
    public function guessFieldType(FieldContext $fieldContext);

    /**
     * Guess the resolve configuration of a field
     *
     * @param FieldContext $fieldContext
     * @return ResolveConfigGuess
     */
    public function guessFieldResolveConfig(FieldContext $fieldContext);
}
