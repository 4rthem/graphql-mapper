<?php
namespace Arthem\GraphQLMapper\Mapping\Driver;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\InterfaceType;
use Arthem\GraphQLMapper\Mapping\QuerySchema;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Mapping\Type;
use Symfony\Component\Yaml\Yaml;

class YamlDriver extends FileDriver
{
    /**
     * @inheritdoc
     */
    public function load(SchemaContainer $schema)
    {
        $paths = $this->getPaths();

        foreach ($paths as $path) {
            $this->loadFile($path, $schema);
        }
    }

    /**
     * @param string          $path
     * @param SchemaContainer $schemaContainer
     */
    private function loadFile($path, SchemaContainer $schemaContainer)
    {
        $config = Yaml::parse($this->getFileContent($path));

        foreach ($config as $type => $node) {
            switch ($type) {
                case 'query':
                    $querySchema = $schemaContainer->getQuerySchema();
                    if (null === $querySchema) {
                        $querySchema = new QuerySchema();
                        $schemaContainer->setQuerySchema($querySchema);
                    }

                    if (isset($node['description'])) {
                        $querySchema->setDescription($node['description']);
                    }

                    if (isset($node['fields'])) {
                        $fields = [];
                        foreach ($node['fields'] as $name => $fieldMapping) {
                            $fields[] = $this->createField($name, $fieldMapping);
                        }
                        $querySchema->setFields($fields);
                    }

                    break;
                case 'types':
                    foreach ($node as $name => $typeMapping) {
                        $type = $this->createType($name, $typeMapping);
                        $schemaContainer->addType($type);
                    }
                    break;
                case 'interfaces':
                    foreach ($node as $name => $interfaceMapping) {
                        $interface = $this->createInterface($name, $interfaceMapping);
                        $schemaContainer->addInterface($interface);
                    }
                    break;
                default:
                    throw new \UnexpectedValueException(sprintf('Unsupported key "%s"'));
                    break;
            }
        }
    }

    /**
     * @param string $name
     * @param array  $mapping
     * @return Type
     */
    private function createType($name, array $mapping)
    {
        $type = new Type();
        $type->setName($name);

        if (isset($mapping['extends'])) {
            $type->setExtends($mapping['extends']);
        }
        if (isset($mapping['description'])) {
            $type->setDescription($mapping['description']);
        }
        if (isset($mapping['fields'])) {
            $fields = [];
            foreach ($mapping['fields'] as $name => $fieldMapping) {
                $fields[] = $this->createField($name, $fieldMapping);
            }
            $type->setFields($fields);
        }

        return $type;
    }

    /**
     * @param string $name
     * @param array  $mapping
     * @return InterfaceType
     */
    private function createInterface($name, array $mapping)
    {
        $interface = new InterfaceType();
        $interface->setName($name);

        if (isset($mapping['description'])) {
            $interface->setDescription($mapping['description']);
        }
        if (isset($mapping['fields'])) {
            $fields = [];
            foreach ($mapping['fields'] as $fieldName => $fieldMapping) {
                $fields[] = $this->createField($fieldName, $fieldMapping);
            }
            $interface->setFields($fields);
        }

        return $interface;
    }

    /**
     * @param string $name
     * @param array  $mapping
     * @return Field
     */
    private function createField($name, array $mapping)
    {
        $field = new Field();
        $field->setName($name);

        if (isset($mapping['type'])) {
            $field->setType($mapping['type']);
        }
        if (isset($mapping['description'])) {
            $field->setDescription($mapping['description']);
        }
        if (isset($mapping['resolve'])) {
            $field->setResolve($mapping['resolve']);
        }
        if (isset($mapping['args'])) {
            $args = [];
            foreach ($mapping['args'] as $argName => $argMapping) {
                $args[] = $this->createField($argName, $argMapping);
            }
            $field->setArguments($args);
        }

        return $field;
    }
}
