<?php

namespace Arthem\GraphQLMapper\Schema;

use Arthem\GraphQLMapper\Mapping\Cache\CacheDriverInterface;
use Arthem\GraphQLMapper\Mapping\Driver\DriverInterface;
use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\FieldContainer;
use Arthem\GraphQLMapper\Mapping\InterfaceType;
use Arthem\GraphQLMapper\Mapping\MappingNormalizer;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Schema\Resolve\CallableResolver;
use Arthem\GraphQLMapper\Schema\Resolve\ResolverInterface;
use GraphQL\Schema;
use GraphQL\Type\Definition as GQLDefinition;

class SchemaFactory
{
    /**
     * @var string
     */
    protected $cacheKey = 'Arthem:GraphQL:Mapping';

    /**
     * @var CacheDriverInterface
     */
    private $cacheDriver;

    /**
     * @var DriverInterface
     */
    private $driver;

    /**
     * @var TypeResolver
     */
    private $typeResolver;

    /**
     * @var ResolverInterface[]
     */
    private $resolveFactories = [];

    /**
     * @var MappingNormalizer
     */
    private $normalizer;

    /**
     * @param DriverInterface $driver
     */
    public function __construct(DriverInterface $driver, TypeResolver $typeResolver)
    {
        $this->driver       = $driver;
        $this->typeResolver = $typeResolver;
        $this->normalizer   = new MappingNormalizer();
        $this->addResolver(new CallableResolver());
    }

    /**
     * @param CacheDriverInterface $cacheDriver
     */
    public function setCacheDriver(CacheDriverInterface $cacheDriver = null)
    {
        $this->cacheDriver = $cacheDriver;
    }

    /**
     * @return Schema
     */
    public function createSchema()
    {
        $schemaContainer = $this->getSchemaContainer();

        foreach ($schemaContainer->getInterfaces() as $type) {
            $GQLType = $this->createInterface($type);
            $this->typeResolver->addType($type->getName(), $GQLType);
        }

        foreach ($schemaContainer->getTypes() as $type) {
            $GQLType = $this->createType($type);
            $this->typeResolver->addType($type->getName(), $GQLType);
        }

        $querySchema  = $schemaContainer->getQuerySchema();
        $mutationType = $schemaContainer->getMutationSchema();
        $queryType    = null !== $querySchema ? $this->createType($querySchema) : null;
        $mutationType = null !== $mutationType ? $this->createType($mutationType) : null;

        return new Schema($queryType, $mutationType);
    }

    /**
     * @return SchemaContainer
     */
    private function getSchemaContainer()
    {
        if (null !== $this->cacheDriver) {
            $schemaContainer = $this->cacheDriver->load();
            if (false !== $schemaContainer) {
                return $schemaContainer;
            }
        }

        return $this->loadSchemaContainer();
    }

    /**
     * @return SchemaContainer
     */
    private function loadSchemaContainer()
    {
        $schemaContainer = new SchemaContainer();
        $this->driver->load($schemaContainer);
        $this->normalizer->normalize($schemaContainer);

        if (null !== $this->cacheDriver) {
            $this->cacheDriver->save($schemaContainer);
        }

        return $schemaContainer;
    }

    /**
     * @param InterfaceType $type
     * @return GQLDefinition\InterfaceType
     */
    private function createInterface(InterfaceType $type)
    {
        if (null !== $type->getFields()) {
            $this->prepareFields($type->getFields());
        }
        $type = new GQLDefinition\InterfaceType($type->toMapping());

        return $type;
    }

    /**
     * @param FieldContainer $type
     * @return GQLDefinition\ObjectType
     */
    private function createType(FieldContainer $type)
    {
        if (null !== $type->getFields()) {
            $this->prepareFields($type->getFields());
        }
        $type = new GQLDefinition\ObjectType($type->toMapping());

        return $type;
    }

    /**
     * @param Field[] $fields
     */
    private function prepareFields(array $fields)
    {
        foreach ($fields as $field) {

            if (null !== $field->getArguments()) {
                $this->prepareFields($field->getArguments());
            }

            $resolveConfig = $field->getResolveConfig();
            if (isset($resolveConfig['handler'])) {
                $handler  = $resolveConfig['handler'];
                $resolver = $this->resolveFactories[$handler]->getFunction($resolveConfig, $field);
                $field->setResolve($resolver);
            }

            $typeName = $field->getType();
            $field->setResolvedType(function () use ($typeName) {
                return $this->typeResolver->resolveType($typeName);
            });
        }
    }

    /**
     * @param ResolverInterface $factory
     */
    public function addResolver(ResolverInterface $factory)
    {
        $this->resolveFactories[$factory->getName()] = $factory;
    }
}
