<?php
namespace Arthem\GraphQLMapper\Resolver;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use GraphQL\Type\Definition\ListOfType;
use GraphQL\Type\Definition\ResolveInfo;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class DoctrineORMResolver implements ResolverInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @var string
     */
    private $className;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param ObjectManager $om
     * @param string        $className
     */
    public function __construct(ObjectManager $om, $className)
    {
        $this->om               = $om;
        $this->className        = $className;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }

    /**
     * @return ObjectRepository
     */
    private function getRepository()
    {
        return $this->om->getRepository($this->className);
    }

    /**
     * @inheritdoc
     */
    public function resolve($node, array $arguments = [], ResolveInfo $info)
    {
        $fieldName = $info->fieldName;
        if (is_object($node) && $this->propertyAccessor->isReadable($node, $fieldName)) {
            return $this->propertyAccessor->getValue($node, $fieldName);
        }

        $repository = $this->getRepository();

        // TODO implement criterias field mapping
        $criterias = $arguments;

        if ($info->returnType instanceof ListOfType) {
            $result = $repository->findBy($criterias);
        } else {
            $result = $repository->findOneBy($criterias);
        }

        return $result;
    }
}
