<?php

namespace Arthem\GraphQLMapper\Schema\Resolve;

use Arthem\GraphQLMapper\Mapping\Field;
use Doctrine\Common\Persistence\ObjectManager;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;

class DoctrineResolver extends SingletonResolver
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'doctrine';
    }

    /**
     * {@inheritdoc}
     */
    protected function createFunction(array $config, Field $field)
    {
        return function ($node, array $arguments, ResolveInfo $info) {
            $resolveConfig = $this->getResolveConfig($info);

            $resolveConfig = $resolveConfig + [
                    'array_params' => false,
                ];

            if (!isset($resolveConfig['method'])) {
                $resolveConfig['method']       = $info->returnType instanceof ListOfType ?
                    'findBy' :
                    'findOneBy';
                $resolveConfig['array_params'] = true;
            }

            $repository = $this->om->getRepository($resolveConfig['entity']);

            $params = $arguments;

            if ($resolveConfig['array_params']) {
                $params = [$params];
            }

            if (null !== $node) {
                array_unshift($params, $node);
            }

            $result = call_user_func_array([$repository, $resolveConfig['method']], $params);

            return $result;
        };
    }
}
