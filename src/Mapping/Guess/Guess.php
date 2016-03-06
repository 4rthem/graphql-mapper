<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

abstract class Guess
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
     * The confidence about the correctness of the value.
     *
     * One of VERY_HIGH_CONFIDENCE, HIGH_CONFIDENCE, MEDIUM_CONFIDENCE
     * and LOW_CONFIDENCE.
     *
     * @var int
     */
    private $confidence;

    /**
     * @param int $confidence
     */
    public function __construct($confidence = self::LOW_CONFIDENCE)
    {
        if (!in_array($confidence, [
            self::VERY_HIGH_CONFIDENCE,
            self::HIGH_CONFIDENCE,
            self::MEDIUM_CONFIDENCE,
            self::LOW_CONFIDENCE,
        ])
        ) {
            throw new \InvalidArgumentException('The confidence should be one of the constants defined in Guess.');
        }
        $this->confidence = $confidence;
    }

    /**
     * @return int
     */
    public function getConfidence()
    {
        return $this->confidence;
    }
}
