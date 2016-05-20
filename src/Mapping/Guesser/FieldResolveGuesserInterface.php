<?php

namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\Context\FieldContext;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\ResolveConfigGuess;

/**
 * Guess the resolve config of a field
 */
interface FieldResolveGuesserInterface extends GuesserInterface
{
    /**
     * Guess the resolve configuration of a field
     *
     * @param FieldContext $fieldContext
     * @return ResolveConfigGuess
     */
    public function guessFieldResolveConfig(FieldContext $fieldContext);
}
