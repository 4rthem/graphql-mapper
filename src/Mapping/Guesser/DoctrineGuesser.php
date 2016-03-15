<?php
namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\Context\ContainerContext;
use Arthem\GraphQLMapper\Mapping\Context\FieldContext;
use Arthem\GraphQLMapper\Mapping\Field;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\Guess;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\TypeGuess;
use Arthem\GraphQLMapper\Mapping\SchemaContainer;
use Arthem\GraphQLMapper\Mapping\Type;
use Doctrine\Common\Persistence\Mapping\ClassMetadataFactory;
use Doctrine\Common\Persistence\Mapping\MappingException;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\DBAL\Types\Type as DoctrineType;
use Doctrine\ORM\Mapping\ClassMetadataInfo;

class DoctrineGuesser implements TypeResolveGuesserInterface, FieldResolveGuesserInterface, FieldTypeGuesserInterface
{
    /**
     * @var ClassMetadataFactory
     */
    private $metadataFactory;

    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->metadataFactory = $objectManager->getMetadataFactory();
    }

    /**
     * {@inheritdoc}
     */
    public function guessFieldType(FieldContext $fieldContext)
    {
        if (!$this->isFieldContainerSupported($fieldContext)) {
            return;
        }

        /** @var Type $fieldContainer */
        $fieldContainer = $fieldContext->getContainer();
        $model          = $fieldContainer->getModel();

        if (null === $metadata = $this->getMetadata($model)) {
            return;
        }

        $field = $fieldContext->getField();

        $property = $field->getProperty() ?: $field->getName();

        if ($metadata->hasAssociation($property)) {
            return $this->guessAssociation($metadata, $field, $fieldContext->getSchema());
        }

        switch ($metadata->getTypeOfField($property)) {
            case DoctrineType::TARRAY:
            case DoctrineType::JSON_ARRAY:
                return $this->wrapRequired($metadata, $property, '[String]', Guess::LOW_CONFIDENCE);
            case DoctrineType::BOOLEAN:
                return new TypeGuess('Boolean', Guess::HIGH_CONFIDENCE);
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

    /**
     * {@inheritdoc}
     */
    public function guessFieldResolveConfig(FieldContext $fieldContext)
    {
        if (!$this->isFieldContainerSupported($fieldContext)) {
            return;
        }

        /** @var Type $type */
        $type = $fieldContext->getContainer();

        return new ResolveConfigGuess([
            'handler' => 'doctrine',
            'entity'  => $type->getModel(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function guessTypeResolveConfig(ContainerContext $containerContext)
    {
        if (!$this->isFieldContainerSupported($containerContext)) {
            return;
        }

        /** @var Type $type */
        $type = $containerContext->getContainer();

        return new ResolveConfigGuess([
            'handler' => 'doctrine',
            'entity'  => $type->getModel(),
        ]);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param string            $property
     * @param string            $type
     * @param int               $confidence
     * @return TypeGuess
     */
    private function wrapRequired(ClassMetadataInfo $metadata, $property, $type, $confidence)
    {
        if (!$metadata->isNullable($property)) {
            $type .= '!';
        }

        return new TypeGuess($type, $confidence);
    }

    /**
     * @param ClassMetadataInfo $metadata
     * @param Field             $field
     * @param SchemaContainer   $schemaContainer
     * @return TypeGuess
     * @throws MappingException
     */
    private function guessAssociation(ClassMetadataInfo $metadata, Field $field, SchemaContainer $schemaContainer)
    {
        $property = $field->getProperty() ?: $field->getName();
        $multiple = $metadata->isCollectionValuedAssociation($property);
        $mapping  = $metadata->getAssociationMapping($property);

        foreach ($schemaContainer->getTypes() as $type) {
            $containerContext = new ContainerContext($type, $schemaContainer);

            if (!$this->isFieldContainerSupported($containerContext)) {
                continue;
            }

            if ($type->getModel() === $mapping['targetEntity']) {
                $typeName = $type->getName();
                if ($multiple) {
                    $typeName = sprintf('[%s]', $typeName);
                }

                return new TypeGuess($typeName, Guess::HIGH_CONFIDENCE);
            }
        }
    }

    /**
     * @param ContainerContext $containerContext
     * @return bool
     */
    private function isFieldContainerSupported(ContainerContext $containerContext)
    {
        $fieldContainer = $containerContext->getContainer();
        if (!$fieldContainer instanceof Type) {
            return false;
        }

        return !empty($fieldContainer->getModel());
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
