<?php

namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\AbstractType;
use Arthem\GraphQLMapper\Mapping\Context\ContainerContext;
use Arthem\GraphQLMapper\Mapping\Context\FieldContext;
use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\Guess;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\ResolveConfigGuess;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\TypeGuess;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Mapping\Type;

class MappingGuesserManager
{
    /**
     * @var GuesserInterface[]
     */
    private $guessers;

    /**
     * @param SchemaContainer $schemaContainer
     */
    public function guess(SchemaContainer $schemaContainer)
    {
        $fieldContainers = $schemaContainer->getTypes() + $schemaContainer->getInterfaces();
        if (null !== $query = $schemaContainer->getQuerySchema()) {
            $fieldContainers[] = $query;
        }

        foreach ($fieldContainers as $fieldContainer) {
            $containerContext = new ContainerContext($fieldContainer, $schemaContainer);
            $this->guessFields($containerContext);
            $this->guessTypeResolveConfig($containerContext);
        }
    }

    /**
     * @param ContainerContext $containerContext
     */
    private function guessFields(ContainerContext $containerContext)
    {
        foreach ($containerContext->getContainer()->getFields() as $field) {
            $fieldContext = $containerContext->createFieldContext($field);
            $this->guessFieldType($fieldContext);
            $this->guessFieldResolveConfig($fieldContext);
        }
    }

    /**
     * @param FieldContext $fieldContext
     */
    private function guessFieldType(FieldContext $fieldContext)
    {
        $field = $fieldContext->getField();
        if ($field->getType()) {
            return;
        }

        $guesses = [];
        foreach ($this->guessers as $guesser) {
            if ($guesser instanceof FieldTypeGuesserInterface) {
                $guess = $guesser->guessFieldType($fieldContext);
                if (null !== $guess) {
                    $guesses[] = $guess;
                }
            }
        }

        /** @var TypeGuess $best */
        $best = $this->getBestGuess($guesses);
        if (null !== $best) {
            $field->setType($best->getType());
        }
    }

    /**
     * @param ContainerContext $containerContext
     */
    private function guessTypeResolveConfig(ContainerContext $containerContext)
    {
        $guesses = [];
        foreach ($this->guessers as $guesser) {
            if ($guesser instanceof TypeResolveGuesserInterface) {
                $guess = $guesser->guessTypeResolveConfig($containerContext);
                if (null !== $guess) {
                    $guesses[] = $guess;
                }
            }
        }

        $this->applyBestGuess($containerContext->getContainer(), $guesses);
    }

    /**
     * @param FieldContext $fieldContext
     */
    private function guessFieldResolveConfig(FieldContext $fieldContext)
    {
        $guesses = [];
        foreach ($this->guessers as $guesser) {
            if ($guesser instanceof FieldResolveGuesserInterface) {
                $guess = $guesser->guessFieldResolveConfig($fieldContext);
                if (null !== $guess) {
                    $guesses[] = $guess;
                }
            }
        }

        $this->applyBestGuess($fieldContext->getField(), $guesses);
    }

    /**
     * @param AbstractType|Type|Field $item
     * @param array                   $guesses
     */
    private function applyBestGuess(AbstractType $item, array $guesses)
    {
        /** @var ResolveConfigGuess $best */
        $best = $this->getBestGuess($guesses);
        if (null !== $best) {
            $item->mergeResolveConfig($best->getConfig());
        }
    }

    /**
     * Return the guess most likely to be correct from a list of guesses
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
     * @param GuesserInterface $guesser
     * @return $this
     */
    public function addGuesser(GuesserInterface $guesser)
    {
        $this->guessers[] = $guesser;

        return $this;
    }
}
