<?php

namespace Arthem\GraphQLMapper\Mapping;

use Arthem\GraphQLMapper\Utils\TypeParser;

class MappingNormalizer
{
    /**
     * Validates mapping and fixes missing definitions
     *
     * @param SchemaContainer $schemaContainer
     */
    public function normalize(SchemaContainer $schemaContainer)
    {
        $this->fixNames($schemaContainer);

        foreach ($schemaContainer->getTypes() as $type) {
            foreach ($type->getFields() as $field) {
                $this->normalizeField($schemaContainer, $field);
            }
        }

        if (null !== $querySchema = $schemaContainer->getQuerySchema()) {
            foreach ($querySchema->getFields() as $field) {
                $this->normalizeField($schemaContainer, $field);
            }
        }

        if (null !== $mutationSchema = $schemaContainer->getMutationSchema()) {
            foreach ($mutationSchema->getFields() as $field) {
                $this->normalizeField($schemaContainer, $field);
            }
        }
    }

    /**
     * @param SchemaContainer $schemaContainer
     */
    private function fixNames(SchemaContainer $schemaContainer)
    {
        if (null !== $query = $schemaContainer->getQuerySchema()) {
            $query->setName('Query');
            if (null === $query->getDescription()) {
                $query->setDescription('The query root of this schema');
            }
        }

        if (null !== $mutation = $schemaContainer->getMutationSchema()) {
            $mutation->setName('Mutation');
            if (null === $mutation->getDescription()) {
                $mutation->setDescription('The mutation root of this schema');
            }
        }
    }

    /**
     * @param SchemaContainer $schemaContainer
     * @param Field           $field
     */
    private function normalizeField(SchemaContainer $schemaContainer, Field $field)
    {
        $config = $field->getResolveConfig();

        // TODO tranform to a Guesser
        if (isset($config['function']) && !isset($config['handler'])) {
            $field->mergeRevolveConfig(['handler' => 'callable']);
        }

        $this->mergeResolveConfig($schemaContainer, $field);
    }

    /**
     * @param SchemaContainer $schemaContainer
     * @param Field           $field
     */
    private function mergeResolveConfig(SchemaContainer $schemaContainer, Field $field)
    {
        $typeName = TypeParser::getFinalType($field->getType());

        if (!$schemaContainer->hasType($typeName)) {
            return;
        }

        $typeConfig = $schemaContainer
            ->getType($typeName)
            ->getResolveConfig();

        $field->mergeResolveConfig($typeConfig);
    }
}
