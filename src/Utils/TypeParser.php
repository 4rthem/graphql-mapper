<?php

namespace Arthem\GraphQLMapper\Utils;

abstract class TypeParser
{
    /**
     * Return the real type if wrapped in a collection
     * "[MyCustomType]!" will return "MyCustomType"
     *
     * @param string $type
     * @return string
     */
    public static function getFinalType($type)
    {
        if (preg_match('#^\[(.+)\]!?$#', $type, $regs)) {
            return $regs[1];
        }

        return $type;
    }
}
