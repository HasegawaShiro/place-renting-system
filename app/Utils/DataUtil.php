<?php
namespace App\Utils;

class DataUtil {
    public static function replaceKeysInMessage(String $message, Array $keys) {
        foreach ($keys as $key => $value) {
            $message = str_replace($key, $value, $message);
        }
        return $message;
    }

    /* public static function sortByKeys($datas, $keys = []) {
        if(self::isCollection($datas)) {
            $datas->sortBy(function($))
        }
    } */

    public static function isCollection($object){
        return is_a($object,'Illuminate\Database\Eloquent\Collection') || is_a($object,'Illuminate\Support\Collection');
    }
}
