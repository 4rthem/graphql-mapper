<?php

namespace Arthem\GraphQLMapper;

use Arthem\GraphQLMapper\Mapping\Driver\DefaultFilePathAccessor;
use Arthem\GraphQLMapper\Mapping\Driver\DriverInterface;
use Arthem\GraphQLMapper\Mapping\Driver\YamlDriver;
use Arthem\GraphQLMapper\Schema\Resolve\DoctrineResolver;
use Arthem\GraphQLMapper\Schema\Resolve\PropertyResolver;
use Arthem\GraphQLMapper\Schema\SchemaFactory;
use Arthem\GraphQLMapper\Schema\TypeResolver;
use Doctrine\Common\Persistence\ObjectManager;

abstract class SchemaSetup
{
    /**
     * @param array $mappingPaths
     */
    public static function createDoctrineYamlSchemaFactory(array $mappingPaths, ObjectManager $om)
    {
        $accessor = new DefaultFilePathAccessor($mappingPaths);
        $driver   = new YamlDriver($accessor);

        return self::createDoctrineSchemaFactory($driver, $om);
    }

    protected static function createDoctrineSchemaFactory(DriverInterface $driver, ObjectManager $om)
    {
        $schemaFactory = new SchemaFactory($driver, self::createTypeResolver());
        $schemaFactory->addResolver(new PropertyResolver());
        $schemaFactory->addResolver(new DoctrineResolver($om));

        return $schemaFactory;
    }

    protected static function createTypeResolver()
    {
        $typeResolver = new TypeResolver();

        return $typeResolver;
    }
}
