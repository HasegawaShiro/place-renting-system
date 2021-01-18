<?php
namespace App\Utils;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DataUtil {
    public static function saveData(Request $request, $model, $page, $mode, $origin = null, &$result, &$status) {
        $data = $request->all();
        if($mode === 'edit') {
            $data[$origin->getKeyName()] = $origin->getKey();
        }
        $validationPass = ValidateUtil::validateForSave($page, $data, $mode, $result);
        $user_id = Auth::check() ? $request->user()->user_id : -1;
        $saved = null;

        if($validationPass) {
            foreach($data as $key => &$value) {
                if(is_a($value, "Illuminate\Http\UploadedFile")) {
                    $fileTemp[$key] = $value;
                    $value = $value->getClientOriginalName();
                }
            }
            $data["updated_by"] = $user_id;
            $old = [];

            if($mode === 'add') {
                $data["created_by"] = $user_id;
                $saved = $model::create($data);
                $data[$saved->getKeyName()] = $saved->getKey();
            } else if($mode === 'edit') {
                foreach($data as $key => $value){
                    if(!$model->isEditable($key)) unset($data[$key]);
                }
                $old = $origin->toArray();
                $origin->update($data);
                $saved = $origin;
            }

            if(method_exists($page,'afterSave')) {
                if(!$page::afterSave($data, $result, $mode, $old)) $status = 422;
            }
        } else {
            $status = 422;
        }

        return $saved;
    }

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

    public static function parseExcelColumn(string $column){
        $result = null;
        if(preg_match("/^[A-Z]+$/",$column) === 1){
            $result = 0;
            for ($i = 1; $i <= strlen($column); $i++) {
                $ASCII = (int) ord($column);
                // dump(($ASCII - 64), pow(26, strlen($column[0]) - $i));
                $result += (($ASCII - 64) * pow(26, strlen($column) - $i)) - 1;
            }
            $result += strlen($column) - 1;
        };

        return $result;
    }

    public static function parseExcelCell(string $cell){
        $result = [];
        $rowPatt = "/[0-9]+$/";
        $columnPatt = "/^[A-Z]+/";
        preg_match($columnPatt,$cell,$column);
        preg_match($rowPatt,$cell,$row);
        // dd($column,$row);
        if(sizeof($column) == 1 && sizeof($row) == 1){
            $result = [
                "row" => (int) $row[0] - 1,
                "column" => DataUtil::parseExcelColumn($column[0])
            ];
        }

        return $result;
    }

    public static function convertToArray($data){
        if (is_object($data)) {
            $data = (array) $data;
        }
        if(is_array($data))
            foreach($data as &$subData){
                $subData = DataUtil::convertToArray($subData);
            }
        return $data;
    }
}
