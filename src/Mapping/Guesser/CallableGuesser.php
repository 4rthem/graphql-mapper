<?php
namespace Arthem\GraphQLMapper\Mapping\Guesser;

use Arthem\GraphQLMapper\Mapping\Context\ContainerContext;
use Arthem\GraphQLMapper\Mapping\Context\FieldContext;

class CallableGuesser implements TypeResolveGuesserInterface, FieldResolveGuesserInterface
{
    /**
     * {@inheritdoc}
     */
    public function guessFieldResolveConfig(FieldContext $fieldContext)
    {
        return $this->guessResolveConfig($fieldContext->getField()->getResolveConfig());
    }

    /**
     * {@inheritdoc}
     */
    public function guessTypeResolveConfig(ContainerContext $containerContext)
    {
        return $this->guessResolveConfig($containerContext->getContainer()->getResolveConfig());
    }

    /**
     * @param array $resolveConfig
     * @return ResolveConfigGuess
     */
    private function guessResolveConfig(array $resolveConfig)
    {
        if (empty($resolveConfig['function'])) {
            return;
        }

        $function = $resolveConfig['function'];

        if (preg_match('#^([\w_]+)\:\:([\w_]+)$#', $resolveConfig['function'], $regs)) {
            $function = [$regs[1], $regs[2]];
        }

        if (!is_callable($function)) {
            throw new \InvalidArgumentException(sprintf('Method or function "%s" is not callable', $resolveConfig['function']));
        }

        return new ResolveConfigGuess([
            'handler'  => 'callable',
            'function' => $function,
        ]);
    }
}
