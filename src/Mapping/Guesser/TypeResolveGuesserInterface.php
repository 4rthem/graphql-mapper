<?php

namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\Context\ContainerContext;

interface TypeResolveGuesserInterface extends GuesserInterface
{
    /**
     * @param ContainerContext $containerContext
     * @return ResolveConfigGuess
     */
    public function guessTypeResolveConfig(ContainerContext $containerContext);
}
