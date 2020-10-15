<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Models\User;
use App\Models\Util;
use App\Models\Place;
use App\Models\Opinion;
use App\Models\Schedule;
use App\Models\Announcement;

use App\Utils\PageUtil;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function test() {
        $result = [[],[],[],[],[],[],[]];
        for ($i=0; $i < 128; $i++) {
            $bin = sprintf("%'07b",$i);
            for ($j=0; $j < 7; $j++) {
                if(substr($bin,$j,1) == "1"){
                    array_push($result[$j], $i);
                }
            }
        }
        dd($result);
    }

    public static function getReferenceSelect($table) {
        $allData = Controller::getDataAsModel($table);
        $id = "{$table}_id";
        $disabled = "{$table}_disabled";
        $name = $table == "user" ? "name" : "{$table}_name";
        $result = [];
        foreach ($allData as $model){
            $data = $model->toArray();
            $putable = true;
            if(array_key_exists($disabled, $data) && $data[$disabled]){ $putable = false; }
            if($putable) $result[$data[$id]] = array_key_exists($name, $data) ? $data[$name] : $data[$id];
        }

        return response()->json($result, 200);
    }

    public static function getDataAsModel(String $table) {
        $table = "App\\Models\\".ucfirst($table);
        $model = new $table();
        $allData = $model::all();

        return $allData;
    }

    public static function getData(Request $request, $table, $id = null) {
        $table = "App\\Models\\".ucfirst($table);
        $model = new $table();
        $result = [
            'datas' => [],
            'message' => '',
        ];
        $status = 200;
        if(is_null($id)){
            foreach($model::all() as $data){
                array_push($result['datas'], $data->toArray());
            }
        }else{
            $data = $model::find($id);
            if(is_null($data)){
                $status = 404;
                $result['message'] = 'data-not-found';
            }else{
                array_push($result['datas'], $data->toArray());
            }
        }

        return response()->json($result, $status);
    }
    public static function postData(Request $request, $table) {
        DB::beginTransaction();
        // dd($request->all());
        $table = "App\\Models\\".ucfirst($table);
        $model = new $table();
        $data = $request->all();
        $validate = PageUtil::validateForSave($table, $data, "add");
        $result = [
            'datas' => [],
            'messages' => null
        ];
        $status = 200;
        if($validate) {
            $data["created_by"] = $request->user()->user_id;
            $data["updated_by"] = $request->user()->user_id;
            $created = $model::create($data);
            $result['messages'] = 'save-success';
        }else{
            $status = 400;
            $result['messages'] = '驗證未通過';
        }
        // dd($created);
        if($status === 200) {
            DB::commit();
        }else{
            DB::rollBack();
        }

        return response()->json($result,200);
    }
    public static function putData(Request $request, $table, $id) {}
    public static function deleteData(Request $request, $table, $id) {}
}
