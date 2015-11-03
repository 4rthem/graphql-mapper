<?php

namespace Arthem\GraphQLMapper\Resolver;

use GraphQL\Type\Definition\ResolveInfo;

interface ResolverInterface
{
    /**
     * @param object      $node
     * @param array       $arguments
     * @param ResolveInfo $info
     * @return mixed
     */
    public function resolve($node, array $arguments = [], ResolveInfo $info);
}
