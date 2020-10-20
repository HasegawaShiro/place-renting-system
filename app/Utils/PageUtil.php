<?php
namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PageUtil {
    public static function validateForSave(String $table, Array &$data, String $status, Array &$result) {
        $class = "App\\Pages\\".ucfirst($table);
        $pass = true;
        if(class_exists($class)){
            $page = new $class();
            $validator = Validator::make($data, $page::fields()[$status], [], $page::fields()['attributes']);

            // return $validator;
            if($validator->fails()){
                foreach($validator->errors()->toArray() as $error){
                    foreach($error as $message){
                        array_push($result['messages'], $message);
                    }
                }
                $pass = false;
            }else{
                if(method_exists($page, 'afterValidation')){
                    $pass = $page::afterValidation($data, $result, $status);
                }
            }
        }

        return $pass;
    }
}
