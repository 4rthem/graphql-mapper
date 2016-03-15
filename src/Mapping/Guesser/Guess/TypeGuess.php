<?php

namespace Arthem\GraphQLMapper\Mapping\Guesser\Guess;

class TypeGuess extends Guess
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $resolveType
     * @param int    $confidence
     */
    public function __construct($resolveType, $confidence = self::LOW_CONFIDENCE)
    {
        $this->type = $resolveType;
        parent::__construct($confidence);
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
}
