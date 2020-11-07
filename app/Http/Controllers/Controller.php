<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Utils\UserUtil;
use App\Utils\PageUtil;
use App\Utils\SessionUtil;
use App\Utils\ValidateUtil;
use App\Utils\ScheduleUtil;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public static function test() {
        /* $result = [[],[],[],[],[],[],[]];
        for ($i=0; $i < 128; $i++) {
            $bin = sprintf("%'07b",$i);
            for ($j=0; $j < 7; $j++) {
                if(substr($bin,$j,1) == "1"){
                    array_push($result[$j], $i);
                }
            }
        }
        dd($result); */
        // dd(\App\Models\User::find(1));
        dump(ScheduleUtil::weekdayChart());
        dd(ScheduleUtil::getDayRepeat(1));
    }

    public static function getReferenceSelect(Request $request, $table) {
        $showDisabled = isset($request->showDisabled) ? $request->showDisabled : false;
        $allData = Controller::getDataAsModel($table);
        $id = "{$table}_id";
        $disabled = "{$table}_disabled";
        $name = $table == "user" ? "name" : "{$table}_name";
        $result = [];
        foreach ($allData as $model){
            $data = $model->toArray();
            $putable = true;
            if(!$showDisabled && array_key_exists($disabled, $data) && $data[$disabled]){ $putable = false; }
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
        $result = [
            'datas' => [],
            'message' => '',
        ];
        $status = 200;
        $filters = isset($request->filters) ? $request->filters : [];
        $orders = isset($request->orders) ? $request->orders : [];

        $class = "App\\Pages\\".ucfirst($table);
        if(class_exists($class)){
            $page = new $class();
            if(method_exists($page, 'getData')){
                $result["datas"] = $page::getData($request, $id);
            }
        }else{
            $class = "App\\Models\\".ucfirst($table);
            $model = new $class();

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
        }

        return response()->json($result, $status);
    }
    public static function postData(Request $request, $table) {
        DB::beginTransaction();
        $class = "App\\Models\\".ucfirst($table);
        $model = new $class();
        $class = "App\\Pages\\".ucfirst($table);
        $page = new $class();
        $permissionPass = method_exists($page, 'permission') ? $page::permission('add') : true;

        $status = 200;
        $result = [
            'datas' => [],
            'messages' => []
        ];
        if($permissionPass){
            $data = $request->all();
            $validationPass = ValidateUtil::validateForSave($table, $data, 'add', $result);

            if($validationPass) {
                $data["created_by"] = $request->user()->user_id;
                $data["updated_by"] = $request->user()->user_id;
                $created = $model::create($data);
                $data[$created->getKeyName()] = $created->getKey();

                if(method_exists($page,'afterSave')){
                    if(!$page::afterSave($data, $result, 'add')) $status = 422;
                }
            }else{
                $status = 422;
            }
        } else {
            $status = 403;
            array_push($result['messages'], 'permission-dinied');
        }

        // $status = 422;

        if($status === 200) {
            array_push($result['messages'], 'save-success');
            DB::commit();
        }else{
            DB::rollBack();
        }

        return response()->json($result,$status);
    }
    public static function putData(Request $request, $table, $id) {
        DB::beginTransaction();
        $class = "App\\Models\\".ucfirst($table);
        $model = new $class();
        $class = "App\\Pages\\".ucfirst($table);
        $page = new $class();
        $origin = $model::find($id);

        $result = [
            'datas' => [],
            'messages' => []
        ];
        $status = 200;

        if(is_null($origin)){
            array_push($result['messages'],'data-not-found');
            $status = 404;
        }else{
            $permissionPass = method_exists($page, 'permission') ? $page::permission('edit', $id) : true;
            if($permissionPass){
                $data = $request->all();
                $data["{$table}_id"] = $id;

                $validationPass = ValidateUtil::validateForSave($table, $data, 'edit', $result);

                if($validationPass) {
                    $toUpdate = $data;
                    $toUpdate["updated_by"] = $request->user()->user_id;
                    foreach($toUpdate as $key => $value){
                        if(!$model->isEditable($key)) unset($toUpdate[$key]);
                    }
                    $old = $origin->toArray();
                    $origin->update($toUpdate);
                    if(method_exists($page,'afterSave')){
                        if(!$page::afterSave($data, $result, 'edit', $old)) $status = 422;
                    }

                }else{
                    $status = 422;
                }
            } else {
                $status = 403;
                array_push($result['messages'], 'permission-dinied');
            }

        }
        // $status = 422;

        if($status === 200) {
            array_push($result['messages'], 'save-success');
            DB::commit();
        }else{
            DB::rollBack();
        }

        return response()->json($result,$status);
    }
    public static function deleteData(Request $request, $table, $id) {
        DB::beginTransaction();
        $result = [
            'messages' => [],
        ];
        $status = 200;
        $class = "App\\Models\\".ucfirst($table);
        $model = new $class();
        $data = $model::find($id);

        if(is_null($data)) {
            $status = 404;
        }else {
            $userValidate = UserUtil::permissionValidate($data);
            if($userValidate) {
                $class = "App\\Pages\\".ucfirst($table);
                if(class_exists($class)){
                    $page = new $class();
                    if(method_exists($page, 'beforeDelete')){
                        if(!$page::beforeDelete($data, $result)) {
                            $status = 400;
                        }else {
                            $model::destroy($id);
                        }
                    }
                }
            }else {
                $status = 403;
                array_push($result['messages'], 'delete-permission-denied');
            }
        }

        if($status === 200){
            array_push($result['messages'], 'delete-success');
            DB::commit();
        }else {
            DB::rollBack();
        }

        return response()->json($result, $status);
    }
}
