<?php

namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\Context\ContainerContext;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\ResolveConfigGuess;

/**
 * Guess the resolve config of a Type or Interface
 */
interface TypeResolveGuesserInterface extends GuesserInterface
{
    /**
     * @param ContainerContext $containerContext
     * @return ResolveConfigGuess
     */
    public function guessTypeResolveConfig(ContainerContext $containerContext);
}
