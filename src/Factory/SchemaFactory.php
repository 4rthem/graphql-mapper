<?php

namespace Arthem\GraphQLMapper\Factory;

use Arthem\GraphQLMapper\Factory\Mapper\MapperFactoryInterface;
use Arthem\GraphQLMapper\Factory\Resolver\ResolverFactoryInterface;
use Arthem\GraphQLMapper\Mapping\Driver\DriverInterface;
use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\InterfaceType;
use Arthem\GraphQLMapper\Mapping\QuerySchema;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Mapping\Type;
use Doctrine\Common\Cache\Cache;
use GraphQL\Schema;
use GraphQL\Type\Definition\InterfaceType as GQLInterfaceType;
use GraphQL\Type\Definition\ObjectType;

class SchemaFactory
{
    /**
     * @var string
     */
    protected $cacheKey = 'Arthem:GraphQL:Mapping';

    /**
     * @var Cache
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
     * @var ResolverFactoryInterface
     */
    private $resolverFactory;

    /**
     * @var MapperFactoryInterface
     */
    private $mapperFactory;

    /**
     * @param DriverInterface $driver
     */
    public function __construct(
        DriverInterface $driver,
        TypeResolver $typeResolver,
        ResolverFactoryInterface $resolverFactory,
        MapperFactoryInterface $mapperFactory
    )
    {
        $this->driver          = $driver;
        $this->typeResolver    = $typeResolver;
        $this->resolverFactory = $resolverFactory;
        $this->mapperFactory   = $mapperFactory;
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

        $querySchema = $schemaContainer->getQuerySchema();
        $queryType   = null !== $querySchema ? $this->createQuerySchema($querySchema) : null;

        return new Schema($queryType);
    }

    /**
     * @return SchemaContainer
     */
    private function getSchemaContainer()
    {
        if (
            !$this->cacheDriver instanceof Cache ||
            false === $schemaContainer = $this->cacheDriver->fetch($this->cacheKey)
        ) {
            $schemaContainer = new SchemaContainer();
            $this->driver->load($schemaContainer);
        }

        return $schemaContainer;
    }

    /**
     * @param InterfaceType $type
     * @return GQLInterfaceType
     */
    private function createInterface(InterfaceType $type)
    {
        if (null !== $type->getFields()) {
            $this->prepareFields($type->getFields());
        }
        $type = new GQLInterfaceType($type->toMapping());

        return $type;
    }

    /**
     * @param Type $type
     * @return ObjectType
     */
    private function createType(Type $type)
    {
        if (null !== $type->getFields()) {
            $this->prepareFields($type->getFields());
        }
        $type = new ObjectType($type->toMapping());

        return $type;
    }

    /**
     * @param QuerySchema $type
     * @return ObjectType
     */
    private function createQuerySchema(QuerySchema $type)
    {
        if (null !== $type->getFields()) {
            $this->prepareFields($type->getFields());
        }
        $type = new ObjectType($type->toMapping());

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

            $field->setMap($this->mapperFactory->createMapper($field));

            $resolver = $this->resolverFactory->createResolver($field);
            $field->setResolve($resolver);

            $typeName = $field->getType();
            $field->setType(function () use ($typeName) {
                return $this->typeResolver->resolveType($typeName);
            });
        }
    }
}
