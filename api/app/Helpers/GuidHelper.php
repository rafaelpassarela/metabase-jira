<?php

namespace App\Helpers;

class GUID
{
    /**
     * Generate an auto GUID number, like
     * 4d38dbff-e745-43c1-96ae-67ba523f04a9
     */
    public static function Generate()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        $newGuid = sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
        return strtolower($newGuid);
    }
}
