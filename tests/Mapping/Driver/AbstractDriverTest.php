<?php

namespace Arthem\GraphQLMapper\Test\Mapping\Driver;

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

    /**
     * @param SchemaContainer $schema
     */
    protected function assertSchemaContainer(SchemaContainer $schema)
    {
        $this->assertEquals($this->getExceptedSchemaContainer(), $schema, 'Invalid schema');
    }

    /**
     * @return SchemaContainer
     */
    private function getExceptedSchemaContainer()
    {
        if (self::$expectedSchema !== null) {
            return self::$expectedSchema;
        }

        $episodeType = new Type();
        $episodeType
            ->setName('Episode')
            ->setDescription('One of the films in the Star Wars Trilogy')
            ->setValues([
                'NEWHOPE' => [
                    'value'       => 4,
                    'description' => 'Released in 1977.',
                ],
                'EMPIRE'  => [
                    'value'       => 5,
                    'description' => 'Released in 1980.',
                ],
                'JEDI'    => [
                    'value'       => 6,
                    'description' => 'Released in 1983.',
                ],
            ]);

        $characterInterface = new InterfaceType();
        $characterInterface
            ->setName('Character')
            ->setDescription('A character in the Star Wars Trilogy')
            ->setFields([
                $this->createIdField('The id of the character.'),
                $this->createNameField('The name of the character.'),
                $this->createFriendsField('The friends of the character, or an empty list if they have none.'),
                $this->createAppearsInField(),
            ]);

        $homePlanet = new Field();
        $homePlanet
            ->setName('homePlanet')
            ->setType('String')
            ->setDescription('The home planet of the human, or null if unknown.');

        $humanType = new Type();
        $humanType->setName('Human')
            ->setDescription('A humanoid creature in the Star Wars universe.')
            ->setExtends('Character')
            ->setFields([
                $this->createIdField('The id of the human.'),
                $this->createNameField('The name of the human.'),
                $this->createFriendsField('The friends of the human, or an empty list if they have none.'),
                $this->createAppearsInField(),
                $homePlanet
            ]);

        $primaryFunction = new Field();
        $primaryFunction
            ->setName('primaryFunction')
            ->setType('String')
            ->setDescription('The primary function of the droid.');

        $droidType = new Type();
        $droidType->setName('Droid')
            ->setDescription('A mechanical creature in the Star Wars universe.')
            ->setExtends('Character')
            ->setFields([
                $this->createIdField('The id of the droid.'),
                $this->createNameField('The name of the droid.'),
                $this->createFriendsField('The friends of the droid, or an empty list if they have none.'),
                $this->createAppearsInField(),
                $primaryFunction
            ]);

        $episodeField = new Field();
        $episodeField
            ->setName('episode')
            ->setType('Episode')
            ->setDescription('If omitted, returns the hero of the whole saga. If provided, returns the hero of that particular episode.');

        $heroField = new Field();
        $heroField->setName('hero')
            ->setType('Character')
            ->setArguments([
                $episodeField,
            ]);

        $humanField = new Field();
        $humanField->setName('human')
            ->setType('Human')
            ->setArguments([
                $this->createIdField('id of the human'),
            ]);

        $droidField = new Field();
        $droidField->setName('droid')
            ->setType('Droid')
            ->setArguments([
                $this->createIdField('id of the droid'),
            ]);

        $query = new Query();
        $query
            ->setFields([
                $heroField,
                $humanField,
                $droidField,
            ]);

        $schema = new SchemaContainer();
        $schema
            ->addType($episodeType)
            ->addType($humanType)
            ->addType($droidType)
            ->addInterface($characterInterface)
            ->setQuerySchema($query);

        return self::$expectedSchema = $schema;
    }

    /**
     * @param $description
     * @return Field
     */
    private function createIdField($description)
    {
        $field = new Field();
        $field
            ->setName('id')
            ->setDescription($description)
            ->setType('String!');

        return $field;
    }

    /**
     * @param $description
     * @return Field
     */
    private function createNameField($description)
    {
        $field = new Field();
        $field
            ->setName('name')
            ->setDescription($description)
            ->setType('String');

        return $field;
    }

    /**
     * @param $description
     * @return Field
     */
    private function createFriendsField($description)
    {
        $field = new Field();
        $field
            ->setName('friends')
            ->setDescription($description)
            ->setType('[Character]');

        return $field;
    }

    /**
     * @return Field
     */
    private function createAppearsInField()
    {
        $field = new Field();
        $field
            ->setName('appearsIn')
            ->setDescription('Which movies they appear in.')
            ->setType('[Episode]');

        return $field;
    }
}
