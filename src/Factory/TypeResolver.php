<?php

namespace Arthem\GraphQLMapper\Factory;

use GraphQL\Type\Definition\Type;

class TypeResolver
{
    /**
     * @var Type[]
     */
    private $types = [];

    /**
     * @param string $name
     * @return Type
     */
    public function resolveType($name)
    {
        if (preg_match('#^(.+)\!$#', $name, $regs)) {
            return Type::nonNull($this->resolveType($regs[1]));
        }

        if (preg_match('#^\[(.+)\]$#', $name, $regs)) {
            return Type::listOf($this->resolveType($regs[1]));
        }

        switch ($name) {
            case Type::INT:
                return Type::int();
                break;
            case Type::STRING:
                return Type::string();
                break;
            case Type::BOOLEAN:
                return Type::boolean();
                break;
            case Type::FLOAT:
                return Type::float();
                break;
            case Type::ID:
                return Type::id();
                break;
            default:
                if (!isset($this->types[$name])) {
                    throw new \InvalidArgumentException(sprintf('Type "%s" is not defined', $name));
                }

                return $this->types[$name];
                break;
        }
    }

    /**
     * @param string $name
     * @param Type   $type
     * @return $this
     */
    public function addType($name, Type $type)
    {
        if (isset($this->types[$name])) {
            throw new \LogicException(sprintf('Type "%s" is already defined', $name));
        }

        $this->types[$name] = $type;

        return $this;
    }
}
