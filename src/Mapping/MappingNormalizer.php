<?php

namespace Arthem\GraphQLMapper\Mapping;

use Arthem\GraphQLMapper\Utils\String;
use Arthem\GraphQLMapper\Utils\TypeParser;

class MappingNormalizer
{
    /**
     * Validates mapping and fixes missing definitions
     * Complete mapping by inspecting the model and guess resolve handlers
     *
     * @param SchemaContainer $schemaContainer
     */
    public function normalize(SchemaContainer $schemaContainer)
    {
        $this->fixNames($schemaContainer);

        foreach ($schemaContainer->getTypes() as $type) {
            foreach ($type->getFields() as $field) {
                $this->normalizeField($schemaContainer, $field, $type);
            }
        }

        if (null !== $querySchema = $schemaContainer->getQuerySchema()) {
            foreach ($querySchema->getFields() as $field) {
                $this->normalizeField($schemaContainer, $field, null);
            }
        }

        if (null !== $mutationSchema = $schemaContainer->getMutationSchema()) {
            foreach ($mutationSchema->getFields() as $field) {
                $this->normalizeField($schemaContainer, $field, null);
            }
        }
    }

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

    private function normalizeField(SchemaContainer $schemaContainer, Field $field, Type $parentType = null)
    {
        $config = $field->getResolveConfig();

        if (empty($config)) {
            if (true === $this->guessProperty($field, $parentType)) {
                return;
            }
        } elseif (isset($config['function']) && !isset($config['handler'])) {
            $field->mergeRevolveConfig(['handler' => 'callable']);
        }

        $this->mergeResolveConfig($schemaContainer, $field);
    }

    private function guessProperty(Field $field, Type $parentType = null)
    {
        if (null === $parentType) {
            return;
        }

        $parentTypeConfig = $parentType->getResolveConfig();
        if (isset($parentTypeConfig['model'])) {
            $className = $parentTypeConfig['model'];
            if (null !== $accessor = $this->getAccessor($className, $field)) {
                $field->mergeRevolveConfig([
                    'method'  => $accessor,
                    'handler' => 'property',
                ]);

                return true;
            }
        }
    }

    private function mergeResolveConfig(SchemaContainer $schemaContainer, Field $field)
    {
        $config   = $field->getResolveConfig();
        $typeName = TypeParser::getFinalType($field->getType());

        if (!$schemaContainer->hasType($typeName)) {
            return;
        }

        $typeConfig = $schemaContainer
            ->getType($typeName)
            ->getResolveConfig();

        $config = array_merge($typeConfig, $config);
        $field->setResolveConfig($config);
    }

    /**
     * @param string $className
     * @param Field  $field
     * @return string|null
     */
    private function getAccessor($className, Field $field)
    {
        $class = new \ReflectionClass($className);

        $property  = $field->getField() ?: $field->getName();
        $camelName = String::camelize($property);

        $getter    = 'get' . $camelName;
        $getsetter = lcfirst($camelName);
        $isser     = 'is' . $camelName;
        $hasser    = 'has' . $camelName;
        $test      = [$getter, $getsetter, $isser, $hasser];

        $reflMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods     = [];
        foreach ($reflMethods as $reflMethod) {
            $methods[$reflMethod->getName()] = true;
        }
        foreach ($test as $method) {
            if (isset($methods[$method])) {
                return $method;
            }
        }
    }
}
