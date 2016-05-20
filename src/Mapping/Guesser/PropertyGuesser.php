<?php
namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\Context\FieldContext;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\Guess;
use Arthem\GraphQLMapper\Mapping\Guesser\Guess\ResolveConfigGuess;
use Arthem\GraphQLMapper\Mapping\Type;
use Arthem\GraphQLMapper\Utils\StringHelper;

class PropertyGuesser implements FieldResolveGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guessFieldResolveConfig(FieldContext $fieldContext)
    {
        /** @var Type $type */
        $type = $fieldContext->getContainer();

        if (!empty($type->getModel())) {
            $className = $type->getModel();
            if (null !== $accessor = $this->getAccessor($className, $fieldContext)) {
                return new ResolveConfigGuess([
                    'method'  => $accessor,
                    'handler' => 'property',
                ], Guess::HIGH_CONFIDENCE);
            }
        }
    }

    /**
     * @param string       $className
     * @param FieldContext $fieldContext
     * @return string|null
     */
    private function getAccessor($className, FieldContext $fieldContext)
    {
        $field = $fieldContext->getField();

        $class = new \ReflectionClass($className);

        $property  = $field->getProperty() ?: $field->getName();
        $camelName = StringHelper::camelize($property);

        $getter    = 'get' . $camelName;
        $getsetter = lcfirst($camelName);
        $isser     = 'is' . $camelName;
        $hasser    = 'has' . $camelName;
        $test      = [$getter, $getsetter, $isser, $hasser];

        $reflMethods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        $methods     = [];
        foreach ($reflMethods as $reflMethod) {
            $methods[$reflMethod->getName()] = true;
        }
        foreach ($test as $method) {
            if (isset($methods[$method])) {
                return $method;
            }
        }
    }
}
