<?php

namespace Arthem\GraphQLMapper;

use Arthem\GraphQLMapper\Exception\QueryException;
use Arthem\GraphQLMapper\Schema\SchemaFactory;
use GraphQL\Executor\ExecutionResult;
use GraphQL\GraphQL;
use GraphQL\Schema;

class GraphQLManager
{
    /**
     * @var SchemaFactory
     */
    private $schemaFactory;

    /**
     * @var Schema
     */
    protected $schema;

    /**
     * @param SchemaFactory $schemaFactory
     */
    public function __construct(SchemaFactory $schemaFactory)
    {
        $this->schemaFactory = $schemaFactory;
    }

    /**
     * @param string      $requestString
     * @param mixed       $rootValue
     * @param array|null  $variableValues
     * @param string|null $operationName
     * @return ExecutionResult
     */
    public function query($requestString, $rootValue = null, $variableValues = null, $operationName = null)
    {
        $schema = $this->getSchema();
        $result = GraphQL::execute($schema, $requestString, $rootValue, $variableValues, $operationName);

        if (is_array($result) && isset($result['errors'])) {
            throw new QueryException($result['errors']);
        }

        return $result;
    }

    /**
     * @return Schema
     */
    private function getSchema()
    {
        if (null === $this->schema) {
            $this->schema = $this->schemaFactory->createSchema();
        }

        return $this->schema;
    }
}
