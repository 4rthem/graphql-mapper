<?php
namespace Arthem\GraphQLMapper\Mapping\Guess;

use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\FieldContainer;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Mapping\Type;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class DoctrineGuesser implements MappingGuesserInterface
{
    /**
     * @var ClassMetadataFactory
     */
    private $metadataFactory;

    /**
     * @param ClassMetadataFactory $metadataFactory
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->metadataFactory = $objectManager->getMetadataFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function guessType(Field $field, FieldContainer $fieldContainer, SchemaContainer $schemaContainer)
    {
        if (!$this->isFieldContainerSupported($fieldContainer)) {
            return;
        }

        /** @var Type $fieldContainer */
        $resolveConfig = $fieldContainer->getResolveConfig();
        $model         = $resolveConfig['model'];

        if (null === $metadata = $this->getMetadata($model)) {
            return;
        }

        $property = $field->getProperty() ?: $field->getName();

        if ($metadata->hasAssociation($property)) {
            return $this->guessAssociation($metadata, $field, $schemaContainer);
        }

        switch ($metadata->getTypeOfField($property)) {
            case DoctrineType::TARRAY:
            case DoctrineType::JSON_ARRAY:
                return $this->wrapRequired($metadata, $property, '[String]', Guess::LOW_CONFIDENCE);
            case DoctrineType::BOOLEAN:
                return new Guess('Boolean', Guess::HIGH_CONFIDENCE);
            case DoctrineType::DATETIME:
            case DoctrineType::DATETIMETZ:
            case 'vardatetime':
            case DoctrineType::DATE:
            case DoctrineType::TIME:
                return $this->wrapRequired($metadata, $property, 'String', Guess::MEDIUM_CONFIDENCE);
            case DoctrineType::DECIMAL:
            case DoctrineType::FLOAT:
                return $this->wrapRequired($metadata, $property, 'Number', Guess::MEDIUM_CONFIDENCE);
            case DoctrineType::INTEGER:
            case DoctrineType::BIGINT:
            case DoctrineType::SMALLINT:
                return $this->wrapRequired($metadata, $property, 'Int', Guess::MEDIUM_CONFIDENCE);
            case DoctrineType::STRING:
                return $this->wrapRequired($metadata, $property, 'String', Guess::MEDIUM_CONFIDENCE);
            case DoctrineType::TEXT:
                return $this->wrapRequired($metadata, $property, 'String', Guess::MEDIUM_CONFIDENCE);
            default:
                return $this->wrapRequired($metadata, $property, 'String', Guess::LOW_CONFIDENCE);
        }
    }

    private function wrapRequired(ClassMetadataInfo $metadata, $property, $type, $confidence)
    {
        if (!$metadata->isNullable($property)) {
            $type .= '!';
        }

        return new Guess($type, $confidence);
    }

    private function guessAssociation(ClassMetadataInfo $metadata, Field $field, SchemaContainer $schemaContainer)
    {
        $property = $field->getProperty() ?: $field->getName();
        $multiple = $metadata->isCollectionValuedAssociation($property);
        $mapping  = $metadata->getAssociationMapping($property);

        foreach ($schemaContainer->getTypes() as $type) {
            if (!$this->isFieldContainerSupported($type)) {
                continue;
            }

            $resolveConfig = $type->getResolveConfig();
            if ($resolveConfig['model'] === $mapping['targetEntity']) {
                $typeName = $type->getName();
                if ($multiple) {
                    $typeName = sprintf('[%s]', $typeName);
                }

                return new Guess($typeName, Guess::HIGH_CONFIDENCE);
            }
        }
    }

    private function isFieldContainerSupported(FieldContainer $fieldContainer)
    {
        if (!$fieldContainer instanceof Type) {
            return false;
        }

        $resolveConfig = $fieldContainer->getResolveConfig();

        return $resolveConfig && isset($resolveConfig['model']);
    }

    /**
     * @param string $class
     * @return ClassMetadataInfo
     */
    protected function getMetadata($class)
    {
        try {
            return $this->metadataFactory->getMetadataFor($class);
        } catch (MappingException $e) {
            // not an entity or mapped super class
        }
    }
}
