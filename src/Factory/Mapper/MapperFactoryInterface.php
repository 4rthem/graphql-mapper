<?php
namespace Arthem\GraphQLMapper\Factory\Mapper;

use Arthem\GraphQLMapper\Mapping\Field;

interface MapperFactoryInterface
{
    /**
     * @param Field $field
     * @return callable|null
     */
    public function createMapper(Field $field);
}
