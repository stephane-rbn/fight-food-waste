<?php

namespace App\Helper;

/**
 * Helper class

 */
class Helper
{
    /**
     * Generate a unique id of an specified length
     *
     * @param int $length
     *
     * @return string
     */
    public static function generateUniqueId(int $length = 32): string
    {
        // Return the hash as a 32-character hexadecimal number
        $id = md5('' . (time() * rand(100, 999)));

        // Return a string composed of the $length first characters
        if (!is_null($length) && is_int($length) && $length <= 32) {
            $id = substr($id, 0, $length);
        }

        return $id;
    }
}
