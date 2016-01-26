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
types:
    User:
        extends: Item
        description: A user, probably a human
        resolve:
            handler: doctrine # Service responsible of retrieving fields data of this type
            model: AppBundle\Entity\User # The class model of this type
        fields:
            id:
                type: Int
                description: The primary key
            name:
                type: String
                description: The user name
            main_email:
                field: email # the model field to read (if different from the key)
                type: String
                description: The user email
            test:
                type: String
                resolve:
                    # handler: callable (optional)
                    function: strtoupper # just a function
                args:
                    my_string: # argument to pass to the "strtoupper" function
                        description: The string to uppercase
                        type: String!
            friends:
                type: "[User]"
                resolve:
                    # handler: doctrine (auto resolved through the "friends" type)
                    model: AppBundle\Entity\Friend # By default, will look at the destination type model
                    method: getFriends # the repository method
                description: The user's friends

interfaces:
    Item:
        description: Something
        fields:
            idd:
                type: Int
                description: The primary key
            name:
                type: String
                description: The thing name

query:
    fields:
        users:
            type: "[User]"
        user:
            type: "User"
            args:
                id:
                    description: The ID
                    type: Int!

mutation:
    fields:
        updateEmail:
            type: User
            resolve:
                method: updateEmail
            args:
                id:
                    description: The ID
                    type: Int!
                email:
                    description: The new email
                    type: String!
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
    luke: user(id: 1) {
        id,
        name,
        main_email,
        test(my_string: "upper me!"),
        friends {
            id, name
        }
    },
    users { id }
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
