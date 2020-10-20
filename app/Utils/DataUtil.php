<?php
namespace App\Utils;

class DataUtil {
    public static function replaceKeysInMessage(String $message, Array $keys) {
        foreach ($keys as $key => $value) {
            $message = str_replace($key, $value, $message);
        }
        return $message;
    }
}
