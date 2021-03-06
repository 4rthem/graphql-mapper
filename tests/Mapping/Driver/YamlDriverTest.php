<?php
namespace Arthem\GraphQLMapper\Test\Mapping\Driver;

use Arthem\GraphQLMapper\Mapping\Driver\YamlDriver;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;

class YamlDriverTest extends AbstractDriverTest
{
    public function testYamlParsing()
    {
        $driver = new YamlDriver(__DIR__ . '/../../data/StarWarsSchema.yml');

        $schema = new SchemaContainer();
        $driver->load($schema);

        $this->assertSchemaContainer($schema);
    }
}
