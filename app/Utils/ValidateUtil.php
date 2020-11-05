<?php
namespace App\Utils;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ValidateUtil {
    public static function validateForSave(String $table, Array &$data, String $status, Array &$result) {
        $class = "App\\Pages\\".ucfirst($table);
        $pass = true;
        if(class_exists($class)){
            $page = new $class();
            $rules = [];
            $fields = $page::fields();
            if(isset($fields[$status])){
                $rules = $fields[$status];
            }else if(isset($fields['add'])){
                $rules = $fields['add'];
            }
            $messages = [];
            if(method_exists($page, 'beforeValidation')){
                $page::beforeValidation($data, $rules, $messages, $status);
            }
            // dd($messages,$fields['attributes']);
            $validator = Validator::make($data, $rules, $messages, $fields['attributes']);

            // return $validator;
            if($validator->fails()){
                foreach($validator->errors()->toArray() as $error){
                    foreach($error as $message){
                        array_push($result['messages'], self::translateDatetimeFormat($message));
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

    public static function translateDatetimeFormat($str) {
        $str = str_replace('Y-m-d', '年-月-日', $str);
        $str = str_replace('H:i', '時:分', $str);
        $str = str_replace(':s', ':秒', $str);

        return $str;
    }
}
