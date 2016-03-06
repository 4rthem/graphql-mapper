<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

use Arthem\GraphQLMapper\Mapping\Context\ContainerContext;

interface TypeGuesserInterface extends GuesserInterface
{
    /**
     * @param ContainerContext $containerContext
     * @return ResolveConfigGuess
     */
    public function guessTypeResolveConfig(ContainerContext $containerContext);
}
