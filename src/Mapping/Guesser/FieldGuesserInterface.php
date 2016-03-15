<?php

namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\Context\FieldContext;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\TypeGuess;

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
