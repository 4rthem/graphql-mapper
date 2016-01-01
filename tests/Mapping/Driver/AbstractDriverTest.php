<?php

namespace Arthem\GraphQLMapper\Test;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\InterfaceType;
use Arthem\GraphQLMapper\Mapping\Query;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Mapping\Type;

abstract class AbstractDriverTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SchemaContainer
     */
    static private $expectedSchema;

    protected function assertSchemaContainer(SchemaContainer $schema)
    {
        $this->assertEquals($this->getExceptedSchemaContainer(), $schema, 'Invalid schema');
    }

    private function getExceptedSchemaContainer()
    {
        if (self::$expectedSchema !== null) {
            return self::$expectedSchema;
        }

        $idField = new Field();
        $idField
            ->setName('id')
            ->setDescription('The user primary key')
            ->setType('Int');

        $nameField = new Field();
        $nameField
            ->setName('name')
            ->setDescription('The user name')
            ->setType('String');

        $emailField = new Field();
        $emailField
            ->setName('email')
            ->setDescription('The user email')
            ->setType('String');

        $friendsField = new Field();
        $friendsField
            ->setName('friends')
            ->setDescription('The user friends')
            ->setType('[User]')
            ->setResolveConfig('AppBundle\Entity\Friend');

        $userType = new Type();
        $userType->setName('User')
            ->setDescription('User type description')
            ->setExtends('Item')
            ->setFields([
                $idField,
                $nameField,
                $emailField,
                $friendsField,
            ]);

        $idField = new Field();
        $idField
            ->setName('id')
            ->setDescription('The item primary key')
            ->setType('Int');

        $nameField = new Field();
        $nameField
            ->setName('name')
            ->setDescription('The item name')
            ->setType('String');

        $interface = new InterfaceType();
        $interface->setName('Item')
            ->setDescription('Item interface description')
            ->setFields([
                $idField,
                $nameField,
            ]);

        $idArg = new Field();
        $idArg->setName('id')
            ->setDescription('The ID')
            ->setType('Int');

        $adminField = new Field();
        $adminField->setName('admin')
            ->setDescription('Admin description')
            ->setType('[User]')
            ->setResolveConfig('AppBundle\Entity\User')
            ->setArguments([
                $idArg,
            ]);

        $idArg = clone $idArg;

        $userField = new Field();
        $userField->setName('user')
            ->setDescription('User description')
            ->setType('User')
            ->setResolveConfig('AppBundle\Entity\User')
            ->setArguments([
                $idArg,
            ]);

        $query = new Query();
        $query->setDescription('The root query description')
            ->setFields([
                $adminField,
                $userField,
            ]);

        $schema = new SchemaContainer();
        $schema
            ->addType($userType)
            ->addInterface($interface)
            ->setQuerySchema($query);

        return self::$expectedSchema = $schema;
    }
}
