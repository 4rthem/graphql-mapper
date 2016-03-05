<?php

namespace Arthem\GraphQLMapper\Utils;

class StringHelper
{
    /**
     * Camelizes a string.
     *
     * @param string $id A string to camelize
     * @return string The camelized string
     */
    public static function camelize($id)
    {
        return strtr(ucwords(strtr($id, ['_' => ' ', '.' => '_ ', '\\' => '_ '])), [' ' => '']);
    }
}
