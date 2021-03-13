<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Utils\FileUtil;
use App\Utils\UserUtil;
use App\Utils\DataUtil;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    public function test() {
        dd(Auth::check());
    }

    public static function getReferenceSelect(Request $request, $table) {
        $showDisabled = isset($request->showDisabled) ? $request->showDisabled : false;
        $filter = isset($request->filter) ? (array) json_decode($request->filter) : [];
        $allData = Controller::getDataAsModel($table);
        $id = "{$table}_id";
        $disabled = "{$table}_disabled";
        $name = $table == "user" ? "name" : "{$table}_name";
        $result = [];
        foreach ($allData as $model){
            $data = $model->toArray();
            $putable = true;
            if(!$showDisabled && array_key_exists($disabled, $data) && $data[$disabled]){
                $putable = false;
            }
            foreach ($filter as $key => $value) {
                if(array_key_exists($key, $data) && $data[$key] != $value) {
                    $putable = false;
                }
            }
            if($putable) $result[$data[$id]] = array_key_exists($name, $data) ? $data[$name] : $data[$id];
        }

        return response()->json($result, 200);
    }

    public static function getDataAsModel(String $table) {
        $table = "App\\Models\\".ucfirst(Str::camel($table));
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
        $orders = isset($request->orders) ? array_map(function($arr) {
            return (array) json_decode($arr);
        }, $request->orders) : [];

        $class = "App\\Pages\\".ucfirst(Str::camel($table));
        $page = new $class();
        if(class_exists($class) && method_exists($page, 'getData')){
            $result["datas"] = $page::getData($request, $id);
        } else {
            $class = "App\\Models\\".ucfirst(Str::camel($table));
            $model = new $class();
            if(is_null($id)){
                $model = $model->query();
                foreach($orders as $order) {
                    $model->orderBy($order["name"], $order["method"]);
                }
                foreach($model->get() as $data){
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
        $class = "App\\Models\\".ucfirst(Str::camel($table));
        $model = new $class();
        $page = $model::getPage();
        $permissionPass = method_exists($page, 'permission') ? $page::permission('add') : true;

        $status = 200;
        $result = [
            'datas' => [],
            'messages' => []
        ];
        $fileTemp = [];
        if($permissionPass){
            $data = DataUtil::parseFormData($request->all());
            $created = DataUtil::saveData($data, $model, $page, 'add', null, $result, $status, $fileTemp);
        } else {
            $status = 403;
            array_push($result['messages'], 'permission-denied');
        }

        // $status = 422;

        if($status === 200) {
            array_push($result['messages'], 'save-success');
            if(isset($data["hasFile"])) {
                foreach($fileTemp as $key => $file) {
                    FileUtil::saveFile($table, $key, $created->getKey(), $file);
                }
            }
            DB::commit();
        }else{
            DB::rollBack();
        }

        return response()->json($result,$status);
    }
    public static function putData(Request $request, $table, $id) {
        DB::beginTransaction();
        $class = "App\\Models\\".ucfirst(Str::camel($table));
        $model = new $class();
        $page = $model::getPage();
        $origin = $model::find($id);

        $status = 200;
        $result = [
            'datas' => [],
            'messages' => []
        ];
        $fileTemp = [];

        if(is_null($origin)) {
            array_push($result['messages'],'data-not-found');
            $status = 404;
        } else {
            $permissionPass = method_exists($page, 'permission') ? $page::permission('edit', $id) : true;
            if($permissionPass) {
                $data = DataUtil::parseFormData($request->all());
                $origin = DataUtil::saveData($data, $model, $page, 'edit', $origin, $result, $status, $fileTemp);
            } else {
                $status = 403;
                array_push($result['messages'], 'permission-denied');
            }

        }
        // $status = 422;

        if($status === 200) {
            array_push($result['messages'], 'save-success');
            if(isset($data["hasFile"])) {
                foreach($fileTemp as $key => $file) {
                    FileUtil::saveFile($table, $key, $origin->getKey(), $file);
                }
            }
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
        $class = "App\\Models\\".ucfirst(Str::camel($table));
        $model = new $class();
        $data = $model::find($id);

        if(is_null($data)) {
            $status = 404;
            array_push($result['messages'], 'data-not-found');
        }else {
            $userValidate = UserUtil::permissionValidate($data);
            if($userValidate) {
                $page = $model::getPage();
                if(!is_null($page)) {
                    if(method_exists($page, 'beforeDelete')) {
                        if(!$page::beforeDelete($data->toArray(), $result, $request->all())) {
                            $status = 400;
                        } else {
                            $model::destroy($id);
                        }
                    } else {
                        $model::destroy($id);
                    }
                } else {
                    $model::destroy($id);
                }
            } else {
                $status = 403;
                array_push($result['messages'], 'delete-permission-denied');
            }
        }

        if($status === 200) {
            array_push($result['messages'], 'delete-success');
            DB::commit();
        } else {
            DB::rollBack();
        }

        return response()->json($result, $status);
    }

    public static function download(Request $request, $table, $id, $filename, $field = null) {
        $class = "App\\Pages\\".ucfirst(Str::camel($table));
        $page = new $class();

        if(method_exists($page, 'getFile')) {
            $filePath = $page::getFile($id, $field, $filename);
        } else {
            $filePath = storage_path("app/uploads/$table/$field/$id-$filename");
        }

        if(file_exists($filePath)) {
            return response()->download($filePath, $filename);
        } else {
            return response()->json(["messages" => 'file-not-found'], 404);
        }
    }
}
