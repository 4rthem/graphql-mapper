<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

class TypeGuess extends Guess
{
    /**
     * @var string
     */
    private $type;

    /**
     * @param string $type
     * @param int    $confidence
     */
    public function __construct($type, $confidence = self::LOW_CONFIDENCE)
    {
        $this->type = $type;
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
