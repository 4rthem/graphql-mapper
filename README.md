# GraphQL Mapper

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/18bf298d-a139-4185-afdb-9226dfd2dc8c/mini.png)](https://insight.sensiolabs.com/projects/18bf298d-a139-4185-afdb-9226dfd2dc8c)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/4rthem/graphql-mapper/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/4rthem/graphql-mapper/?branch=master)

### Work in progress.

This library allows to build a GraphQL schema based on your model.
It depends on the [GraphQL PHP implementation](https://github.com/webonyx/graphql-php)

## Installation

This is installable via [Composer](https://getcomposer.org/) as [arthem/graphql-mapper](https://packagist.org/packages/arthem/graphql-mapper):

```bash
composer require arthem/graphql-mapper
```

## Setup / Configuration

Create your schema:

```yaml
# /path/to/your/mapping/file.yml

interfaces:
    Character:
        description: A character in the Star Wars Trilogy
        fields:
            id:
                type: String!
                description: The id of the character.
            name:
                type: String
                description: The name of the character.
            friends:
                type: "[Character]"
                description: The friends of the character, or an empty list if they have none.
            appearsIn:
                type: "[Episode]"
                description: Which movies they appear in.

types:
    Episode:
        description: One of the films in the Star Wars Trilogy
        values:
            NEWHOPE:
                value: 4
                description: Released in 1977.
            EMPIRE:
                value: 5
                description: Released in 1980.
            JEDI:
                value: 6
                description: Released in 1983.

    Human:
        description: A humanoid creature in the Star Wars universe.
        extends: Character
        fields:
            id:
                type: String!
                description: The id of the human.
            name:
                type: String
                description: The name of the human.
            friends:
                type: "[Character]"
                description: The friends of the human, or an empty list if they have none.
            appearsIn:
                type: "[Episode]"
                description: Which movies they appear in.
            homePlanet:
                type: String
                description: The home planet of the human, or null if unknown.

    Droid:
        description: A mechanical creature in the Star Wars universe.
        extends: Character
        fields:
            id:
                type: String!
                description: The id of the droid.
            name:
                type: String
                description: The name of the droid.
            friends:
                type: "[Character]"
                description: The friends of the droid, or an empty list if they have none.
            appearsIn:
                type: "[Episode]"
                description: Which movies they appear in.
            primaryFunction:
                type: String
                description: The primary function of the droid.


query:
    fields:
        hero:
            type: Character
            args:
                episode:
                    description: If omitted, returns the hero of the whole saga. If provided, returns the hero of that particular episode.
                    type: Episode
        human:
            type: Human
            args:
                id:
                    description: id of the human
                    type: String!
        droid:
            type: Droid
            args:
                id:
                    description: id of the droid
                    type: String!
        date:
            type: "[String]"
            description: The current time
            resolve:
                function: getdate
                no_args: true # no context arguments will be passed to the function

mutation:
    fields:
        createDroid:
            type: Droid
            resolve:
                method: createDroid
            args:
                name:
                    type: String
                    description: The name of the droid.
                friends:
                    type: "[Character]"
                    description: The friends of the droid.
                appearsIn:
                    type: "[Episode]"
                    description: Which movies they appear in.
                primaryFunction:
                    type: String
                    description: The primary function of the droid.
```

> NB: listOf types must be wrapped by quotes `type: "[User]"`

## Usage

```php
// entry.php
use Arthem\GraphQLMapper\GraphQLManager;
use Arthem\GraphQLMapper\SchemaSetup;

// bootstrap.php
require_once '../vendor/autoload.php';

// replace with mechanism to retrieve Doctrine EntityManager in your app
$entityManager = getEntityManager();

// GraphQL part
$paths          = ['/path/to/your/mapping/file.yml'];
$schemaFactory  = SchemaSetup::createDoctrineYamlSchemaFactory($paths, $entityManager);
$graphQLManager = new GraphQLManager($schemaFactory);

$data = $graphQLManager->query($_POST['query']);
```

Ready to query:

```bash
curl -XPOST 'http://localhost/entry.php' -d 'query=query FooBar {
    luke: hero(id: 1) {
        id,
        name,
        friends {
            id, name
        }
    },
    droid(id: 2) {
        primaryFunction
    }
}'
```

## Custom Resolver

Resolvers are responsible for creating function (Closure) to resolve the data.
The way to use a specific factory is to define the `handler` key in the `resolve` node.
Internal handlers are: `property`, `callable` and `doctrine`.

But you can define your own!

Create your `CustomResolver` that implements `Arthem\GraphQLMapper\Schema\Resolve\ResolverInterface`

Then register it to the `SchemaFactory`:

```php
$schemaFactory  = SchemaSetup::createDoctrineYamlSchemaFactory($paths, $entityManager);
$schemaFactory->addResolver(new CustomResolver());
```

## License

Released under the [MIT License](LICENSE).
