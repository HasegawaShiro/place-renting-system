<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Box\Spout\Common\Entity\Row;
use Box\Spout\Writer\Common\Creator\WriterEntityFactory;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

use App\Utils\FileUtil;
use App\Utils\UserUtil;
use App\Utils\DataUtil;
use App\Utils\SessionUtil;

use App\Models\Place;
use App\Models\Schedule;

class Controller extends BaseController {
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
  public function test() {}

  public static function getReferenceSelect(Request $request, $table)
  {
    $showDisabled = $request->showDisabled ?? false;
    $filter = isset($request->filter) ? (array) json_decode($request->filter) : [];
    $allData = Controller::getDataAsModel($table);
    $id = "{$table}_id";
    $disabled = "{$table}_disabled";
    $name = $table == "user" ? "name" : "{$table}_name";
    $result = [];
    foreach ($allData as $model) {
      $data = $model->toArray();
      $putable = true;
      if (!$showDisabled && array_key_exists($disabled, $data) && $data[$disabled]) {
        $putable = false;
      }
      foreach ($filter as $key => $value) {
        if (array_key_exists($key, $data) && $data[$key] != $value) {
          $putable = false;
        }
      }
      if ($putable) $result[$data[$id]] = array_key_exists($name, $data) ? $data[$name] : $data[$id];
    }

    return response()->json($result, 200);
  }

  public static function getDataAsModel(String $table)
  {
    $table = "App\\Models\\" . ucfirst(Str::camel($table));
    $model = new $table();
    $allData = $model::all();

    return $allData;
  }

  public static function getData(Request $request, $table, $id = null)
  {
    $result = [
      'datas' => [],
      'message' => '',
    ];
    $status = 200;
    $filters = $request->filters ?? [];
    $orders = isset($request->orders) ? array_map(function ($arr) {
      return (array) json_decode($arr);
    }, $request->orders) : [];

    $class = "App\\Pages\\" . ucfirst(Str::camel($table));
    $page = new $class();
    if (class_exists($class) && method_exists($page, 'getData')) {
      $result["datas"] = $page::getData($request, $id);
    } else {
      $class = "App\\Models\\" . ucfirst(Str::camel($table));
      $model = new $class();
      if ($id === null) {
        $model = $model->query();
        foreach ($orders as $order) {
          $model->orderBy($order["name"], $order["method"]);
        }
        foreach ($model->get() as $data) {
          array_push($result['datas'], $data->toArray());
        }
      } else {
        $data = $model::find($id);
        if ($data === null) {
          $status = 404;
          $result['message'] = 'data-not-found';
        } else {
          array_push($result['datas'], $data->toArray());
        }
      }
    }

    return response()->json($result, $status);
  }
  public static function postData(Request $request, $table) {
    DB::beginTransaction();
    $class = "App\\Models\\" . ucfirst(Str::camel($table));
    $model = new $class();
    $page = $model::getPage();
    $permissionPass = method_exists($page, 'permission') ? $page::permission('add') : true;

    $status = 200;
    $result = [
      'datas' => [],
      'messages' => []
    ];
    $fileTemp = [];
    if ($permissionPass) {
      $data = DataUtil::parseFormData($request->all());
      $created = DataUtil::saveData($data, $model, $page, 'add', null, $result, $status, $fileTemp);
    } else {
      $status = 403;
      array_push($result['messages'], 'permission-denied');
    }

    // $status = 422;

    if ($status === 200) {
      array_push($result['messages'], 'save-success');
      if (isset($data["hasFile"])) {
        foreach ($fileTemp as $key => $file) {
          FileUtil::saveFile($table, $key, $created->getKey(), $file);
        }
      }
      DB::commit();
    } else {
      DB::rollBack();
    }

    return response()->json($result, $status);
  }
  public static function putData(Request $request, $table, $id)
  {
    DB::beginTransaction();
    $class = "App\\Models\\" . ucfirst(Str::camel($table));
    $model = new $class();
    $page = $model::getPage();
    $origin = $model::find($id);

    $status = 200;
    $result = [
      'datas' => [],
      'messages' => []
    ];
    $fileTemp = [];

    if ($origin === null) {
      array_push($result['messages'], 'data-not-found');
      $status = 404;
    } else {
      $permissionPass = method_exists($page, 'permission') ? $page::permission('edit', $id) : true;
      if ($permissionPass) {
        $data = DataUtil::parseFormData($request->all());
        $origin = DataUtil::saveData($data, $model, $page, 'edit', $origin, $result, $status, $fileTemp);
      } else {
        $status = 403;
        array_push($result['messages'], 'permission-denied');
      }
    }
    // $status = 422;

    if ($status === 200) {
      array_push($result['messages'], 'save-success');
      if (isset($data["hasFile"])) {
        foreach ($fileTemp as $key => $file) {
          FileUtil::saveFile($table, $key, $origin->getKey(), $file);
        }
      }
      DB::commit();
    } else {
      DB::rollBack();
    }

    return response()->json($result, $status);
  }
  public static function deleteData(Request $request, $table, $id)
  {
    DB::beginTransaction();
    $result = [
      'messages' => [],
    ];
    $status = 200;
    $class = "App\\Models\\" . ucfirst(Str::camel($table));
    $model = new $class();
    $data = $model::find($id);

    if (is_null($data)) {
      $status = 404;
      array_push($result['messages'], 'data-not-found');
    } else {
      $userValidate = UserUtil::permissionValidate($data);
      if ($userValidate) {
        $page = $model::getPage();
        if (!is_null($page)) {
          if (method_exists($page, 'beforeDelete')) {
            if (!$page::beforeDelete($data->toArray(), $result, $request->all())) {
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

    if ($status === 200) {
      array_push($result['messages'], 'delete-success');
      DB::commit();
    } else {
      DB::rollBack();
    }

    return response()->json($result, $status);
  }

  public static function download(Request $request, $table, $id, $filename, $field = null)
  {
    $class = "App\\Pages\\" . ucfirst(Str::camel($table));
    $page = new $class();

    $filePath = method_exists($page, 'getFile')
      ? $page::getFile($id, $field, $filename)
      : storage_path("app/uploads/$table/$field/$id-$filename");

    if (file_exists($filePath)) {
      return response()->download($filePath, $filename);
    } else {
      return response()->json(["messages" => 'file-not-found'], 404);
    }
  }

  public static function exportSchedules(Request $request) {
    $invalidFilters = function($message) {
      return response()->json($message, 400);
    };
    $FROM_TO_LIMIT = 6;
    $types = [
      "conference" => '會議',
      "activity" => '活動',
      "lesson" => '課程',
      "exam" => '考試',
      "lecture" => '講座',
      "camp" => '營隊',
      "other" => '其他',
    ];

    $user = SessionUtil::getLoginUser();

    if ($user["id"] !== 1) return response()->json(null, 401);

    $filters = DataUtil::parseFormData($request->all());
    $dateFrom = $filters["dateFrom"];
    $dateTo = $filters["dateTo"];
    $placeFrom = $filters["placeFrom"];
    $placeTo = $filters["placeTo"];

    if (empty($dateFrom) || empty($dateTo)) {
      return $invalidFilters("日期起迄皆為必填。");
    }

    $from = new Carbon($dateFrom);
    $to = new Carbon($dateTo);

    if ($from->diffInMonths($to) > $FROM_TO_LIMIT) {
      return $invalidFilters("日期範圍請勿超過 $FROM_TO_LIMIT 個月");
    }

    $sorts = [
      [
        'name' => 'schedule_date',
        'method' => 'DESC'
      ],
      [
        'name' => 'schedule_from',
        'method' => 'ASC'
      ],
      [
        'name' => 'schedule_to',
        'method' => 'ASC'
      ],
    ];
    $schedules = Schedule::with(['util', 'user', 'place']);
    $schedules->whereDate("schedule_date", '>=', $dateFrom);
    $schedules->whereDate("schedule_date", '<=', $dateTo);


    $places = (new Place())->query();
    $hasPlaceFrom = $placeFrom === 0 || $placeFrom === "0" || !empty($placeFrom);
    $hasPlaceTo = $placeTo === 0 || $placeTo === "0" || !empty($placeTo);
    if($hasPlaceFrom || $hasPlaceTo) {
      if($hasPlaceFrom) $places->where('place_code', '>=', $placeFrom);
      if($hasPlaceTo) $places->where('place_code', '<=', $placeTo);
    }
    $places = $places->get()->map(function ($p) { return $p->place_id; })->toArray();

    $schedules->whereIn('place_id', $places);

    foreach ($sorts as $sort) {
      $schedules->orderBy($sort["name"], $sort['method']);
    }

    $data = $schedules->get();

    if(sizeof($data) === 0) {
      return response()->json(null, 404);
    }

    $output = public_path('xlsx');
    $filename = "$dateFrom~$dateTo.xlsx";
    $timestamp = Carbon::now()->timestamp;
    $path = "$output/$timestamp$filename";
    FileUtil::newFolder($output);

    $writer = WriterEntityFactory::createXLSXWriter();
    $writer->openToFile($path);
    $writer->addRow(
      WriterEntityFactory::createRowFromArray([
        '主題','日期','時間','地點','登記人','借用型態','內容','承辦人',
        '承辦單位','電話','電子信箱','聯絡人','相關網址',
      ])
    );

    foreach ($data as $datum) {
      $writer->addRow(
        WriterEntityFactory::createRowFromArray([
          $datum->schedule_title,
          $datum->schedule_date->format("Y-m-d"),
          (
            $datum->schedule_from->format("H:i").
            "~".
            $datum->schedule_to->format("H:i")
          ),
          $datum->place->place_name,
          $datum->schedule_registrant,
          $types[$datum->schedule_type],
          $datum->schedule_content,
          $datum->user->name,
          $datum->util->util_name,
          $datum->user->phone,
          $datum->user->email,
          $datum->schedule_contact,
          $datum->schedule_url,
        ])
      );
    }

    $writer->close();
    return response()->download($path, $filename)->deleteFileAfterSend();
  }
}
