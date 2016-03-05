<?php

namespace Arthem\GraphQLMapper\Mapping\Guess;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\FieldContainer;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;

class MappingGuesserManager
{
    /**
     * @var MappingGuesserInterface[]
     */
    private $guessers;

    public function guess(SchemaContainer $schemaContainer)
    {
        foreach ($schemaContainer->getTypes() as $type) {
            $this->guessFields($type, $schemaContainer);
        }
    }

    private function guessFields(FieldContainer $fieldContainer, SchemaContainer $schemaContainer)
    {
        foreach ($fieldContainer->getFields() as $field) {
            $this->guessType($field, $fieldContainer, $schemaContainer);
        }
    }

    private function guessType(Field $field, FieldContainer $fieldContainer, SchemaContainer $schemaContainer)
    {
        if ($field->getType()) {
            return;
        }

        $guesses = [];
        foreach ($this->guessers as $guesser) {
            $guess = $guesser->guessType($field, $fieldContainer, $schemaContainer);
            if (null !== $guess) {
                $guesses[] = $guess;
            }
        }

        $best = $this->getBestGuess($guesses);
        if (null !== $best) {
            $field->setType($best->getValue());
        }
    }

    /**
     * Returns the guess most likely to be correct from a list of guesses.
     *
     * If there are multiple guesses with the same, highest confidence, the
     * returned guess is any of them.
     *
     * @param Guess[] $guesses An array of guesses
     * @return Guess|null The guess with the highest confidence
     */
    public function getBestGuess(array $guesses)
    {
        $result        = null;
        $maxConfidence = -1;

        foreach ($guesses as $guess) {
            if ($maxConfidence < $confidence = $guess->getConfidence()) {
                $maxConfidence = $confidence;
                $result        = $guess;
            }
        }

        return $result;
    }

    /**
     * @param MappingGuesserInterface $guesser
     * @return $this
     */
    public function addGuesser(MappingGuesserInterface $guesser)
    {
        $this->guessers[] = $guesser;

        return $this;
    }
}
