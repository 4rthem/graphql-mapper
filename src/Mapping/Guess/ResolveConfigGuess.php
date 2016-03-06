<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

class ResolveConfigGuess extends Guess
{
    /**
     * @var array
     */
    private $config;

    /**
     * @param array $config
     * @param int   $confidence
     */
    public function __construct(array $config, $confidence = self::LOW_CONFIDENCE)
    {
        $this->config = $config;
        parent::__construct($confidence);
    }

    /**
     * @return array
     */
    public function getConfig()
    {
        return $this->config;
    }
}
