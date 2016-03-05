<?php
namespace Arthem\GraphQLMapper\Mapping\Driver;

use Arthem\GraphQLMapper\Mapping\AbstractType;
use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\FieldContainer;
use Arthem\GraphQLMapper\Mapping\InterfaceType;
use Arthem\GraphQLMapper\Mapping\Query;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Mapping\Type;
use Symfony\Component\Yaml\Yaml;

class YamlDriver extends FileDriver
{
    /**
     * {@inheritdoc}
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

        foreach ($config as $type => $mapping) {
            switch ($type) {
                case 'query':
                    $this->mapQuery($schemaContainer, $mapping);
                    break;
                case 'mutation':
                    $this->mapMutation($schemaContainer, $mapping);
                    break;
                case 'types':
                    $this->mapTypes($schemaContainer, $mapping);
                    break;
                case 'interfaces':
                    $this->mapInterfaces($schemaContainer, $mapping);
                    break;
                default:
                    throw new \UnexpectedValueException(sprintf('Unsupported key "%s"', $type));
            }
        }
    }

    /**
     * @param SchemaContainer $schemaContainer
     * @param array           $mapping
     */
    private function mapQuery(SchemaContainer $schemaContainer, array $mapping)
    {
        $querySchema = $schemaContainer->getQuerySchema();
        if (null === $querySchema) {
            $querySchema = new Query();
            $schemaContainer->setQuerySchema($querySchema);
        }

        $this->populateFieldContainer($querySchema, $mapping);
    }

    /**
     * @param SchemaContainer $schemaContainer
     * @param array           $mapping
     */
    private function mapMutation(SchemaContainer $schemaContainer, array $mapping)
    {
        $mutationSchema = $schemaContainer->getMutationSchema();
        if (null === $mutationSchema) {
            $mutationSchema = new Query();
            $schemaContainer->setMutationSchema($mutationSchema);
        }

        $this->populateFieldContainer($mutationSchema, $mapping);
    }

    /**
     * @param SchemaContainer $schemaContainer
     * @param array           $mapping
     */
    private function mapTypes(SchemaContainer $schemaContainer, array $mapping)
    {
        foreach ($mapping as $name => $typeMapping) {
            $type = $this->createType($name, $typeMapping);
            $schemaContainer->addType($type);
        }
    }

    /**
     * @param SchemaContainer $schemaContainer
     * @param array           $mapping
     */
    private function mapInterfaces(SchemaContainer $schemaContainer, array $mapping)
    {
        foreach ($mapping as $name => $interfaceMapping) {
            $interface = $this->createInterface($name, $interfaceMapping);
            $schemaContainer->addInterface($interface);
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
        $type
            ->setName($name)
            ->setExtends(isset($mapping['extends']) ? $mapping['extends'] : null)
            ->setResolveConfig(isset($mapping['resolve']) ? $mapping['resolve'] : null);

        if (isset($mapping['values'])) {
            $type->setValues($mapping['values']);
        }

        $this->populateFieldContainer($type, $mapping);

        return $type;
    }

    /**
     * @param AbstractType $type
     * @param array        $mapping
     */
    private function populateType(AbstractType $type, array $mapping)
    {
        if (isset($mapping['description'])) {
            $type->setDescription($mapping['description']);
        }
    }

    /**
     * @param FieldContainer $type
     * @param array          $mapping
     */
    private function populateFieldContainer(FieldContainer $type, array $mapping)
    {
        $this->populateType($type, $mapping);

        if (!isset($mapping['fields'])) {
            return;
        }

        $fields = [];
        foreach ($mapping['fields'] as $name => $fieldMapping) {
            $fields[] = $this->createField($name, $fieldMapping);
        }
        $type->setFields($fields);
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
        $this->populateFieldContainer($interface, $mapping);

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
        $field
            ->setName($name)
            ->setType(isset($mapping['type']) ? $mapping['type'] : null)
            ->setProperty(isset($mapping['property']) ? $mapping['property'] : null)
            ->setResolveConfig(isset($mapping['resolve']) ? $mapping['resolve'] : []);

        $this->populateType($field, $mapping);

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
