<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

class Guess
{
    /**
     * Marks an instance with a value that is extremely likely to be correct.
     *
     * @var int
     */
    const VERY_HIGH_CONFIDENCE = 3;

    /**
     * Marks an instance with a value that is very likely to be correct.
     *
     * @var int
     */
    const HIGH_CONFIDENCE = 2;

    /**
     * Marks an instance with a value that is likely to be correct.
     *
     * @var int
     */
    const MEDIUM_CONFIDENCE = 1;

    /**
     * Marks an instance with a value that may be correct.
     *
     * @var int
     */
    const LOW_CONFIDENCE = 0;

    /**
     * @var mixed
     */
    private $value;

    /**
     * The confidence about the correctness of the value.
     *
     * One of VERY_HIGH_CONFIDENCE, HIGH_CONFIDENCE, MEDIUM_CONFIDENCE
     * and LOW_CONFIDENCE.
     *
     * @var int
     */
    private $confidence;

    /**
     * Guess constructor.
     *
     * @param mixed $value
     * @param int   $confidence
     */
    public function __construct($value, $confidence = self::LOW_CONFIDENCE)
    {
        $this->value      = $value;
        $this->confidence = $confidence;
    }

}
