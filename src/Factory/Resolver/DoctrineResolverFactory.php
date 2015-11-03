<?php

namespace Arthem\GraphQLMapper\Factory\Resolver;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Resolver\DoctrineORMResolver;
use Doctrine\Common\Persistence\Mapping\ClassMetadata;
use Doctrine\Common\Persistence\ObjectManager;
use GraphQL\Type\Definition\ResolveInfo;

class DoctrineResolverFactory implements ResolverFactoryInterface
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
     * @inheritdoc
     */
    public function createResolver(Field $field)
    {
        $resolve = $field->getResolve();
        if (null === $resolve) {
            return;
        }
        switch (true) {
            case is_callable($resolve):
                return $resolve;
                break;
            case class_exists($resolve):
                return $this->createDoctrineResolver($resolve);
                break;
            default:
                $metadata = $this->om->getClassMetadata($resolve);
                if ($metadata instanceof ClassMetadata) {
                    return $this->createDoctrineResolver($resolve);
                }

                throw new \InvalidArgumentException(sprintf('Resolver is "%s" not defined', $resolve));
                break;
        }
    }

    /**
     * @param string $className
     * @return \Closure
     */
    private function createDoctrineResolver($className)
    {
        $resolver = new DoctrineORMResolver($this->om, $className);

        return function ($node, array $arguments = [], ResolveInfo $info) use ($resolver) {
            $item = $resolver->resolve($node, $arguments, $info);

            return $item;
        };
    }
}
