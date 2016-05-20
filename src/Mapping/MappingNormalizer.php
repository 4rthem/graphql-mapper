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

        $fields = [];

        foreach ($schemaContainer->getTypes() as $type) {
            $fields = array_merge($fields, $type->getFields());
        }

        if (null !== $querySchema = $schemaContainer->getQuerySchema()) {
            $fields = array_merge($fields, $querySchema->getFields());
        }

        if (null !== $mutationSchema = $schemaContainer->getMutationSchema()) {
            $fields = array_merge($fields, $mutationSchema->getFields());
        }

        foreach ($fields as $field) {
            $this->mergeResolveConfig($schemaContainer, $field);
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
     * Apply the resolve config of types that are used by query fields
     *
     * @param SchemaContainer $schemaContainer
     * @param Field           $field
     */
    private function mergeResolveConfig(SchemaContainer $schemaContainer, Field $field)
    {
        $typeName = TypeParser::getFinalType($field->getType());

        if ($schemaContainer->hasType($typeName)) {
            $typeConfig = $schemaContainer
                ->getType($typeName)
                ->getResolveConfig();
        } elseif ($schemaContainer->hasInterface($typeName)) {
            $typeConfig = $schemaContainer
                ->getInterface($typeName)
                ->getResolveConfig();
        } else {
            return;
        }

        $field->mergeResolveConfig($typeConfig);
    }
}
