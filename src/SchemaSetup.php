<?php

namespace Arthem\GraphQLMapper;

use Arthem\GraphQLMapper\Mapping\Driver\DefaultFilePathAccessor;
use Arthem\GraphQLMapper\Mapping\Driver\DriverInterface;
use Arthem\GraphQLMapper\Mapping\Driver\YamlDriver;
use Arthem\GraphQLMapper\Mapping\Guesser\CallableGuesser;
use Arthem\GraphQLMapper\Mapping\Guesser\DoctrineGuesser;
use Arthem\GraphQLMapper\Mapping\Guesser\MappingGuesserManager;
use Arthem\GraphQLMapper\Mapping\Guesser\PropertyGuesser;
use Arthem\GraphQLMapper\Schema\Resolve\DoctrineResolver;
use Arthem\GraphQLMapper\Schema\Resolve\PropertyResolver;
use Arthem\GraphQLMapper\Schema\SchemaFactory;
use Arthem\GraphQLMapper\Schema\TypeResolver;
use Doctrine\Common\Persistence\ObjectManager;

abstract class SchemaSetup
{
    /**
     * @param array         $mappingPaths
     * @param ObjectManager $om
     * @return SchemaFactory
     */
    public static function createDoctrineYamlSchemaFactory(array $mappingPaths, ObjectManager $om)
    {
        $accessor = new DefaultFilePathAccessor($mappingPaths);
        $driver   = new YamlDriver($accessor);

        return self::createDoctrineSchemaFactory($driver, $om);
    }

    /**
     * @return MappingGuesserManager
     */
    protected static function createDefaultMappingGuesserManager()
    {
        $mappingGuesser = new MappingGuesserManager();
        $mappingGuesser->addGuesser(new CallableGuesser());
        $mappingGuesser->addGuesser(new PropertyGuesser());

        return $mappingGuesser;
    }

    /**
     * @param DriverInterface $driver
     * @param ObjectManager   $om
     * @return SchemaFactory
     */
    protected static function createDoctrineSchemaFactory(DriverInterface $driver, ObjectManager $om)
    {
        $mappingGuesser = self::createDefaultMappingGuesserManager();
        $mappingGuesser->addGuesser(new DoctrineGuesser($om));

        $schemaFactory = new SchemaFactory($driver, self::createTypeResolver(), $mappingGuesser);
        $schemaFactory->addResolver(new PropertyResolver());
        $schemaFactory->addResolver(new DoctrineResolver($om));

        return $schemaFactory;
    }

    /**
     * @return TypeResolver
     */
    protected static function createTypeResolver()
    {
        $typeResolver = new TypeResolver();

        return $typeResolver;
    }
}
