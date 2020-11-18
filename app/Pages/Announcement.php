<?php
namespace App\Pages;

use Illuminate\Validation\Rule;

use App\Models\Announcement as _MODEL;

use App\Utils\SessionUtil;

class Announcement {
    public static function fields() {
        return [
            'add' => [
                'announcement_title' => [
                    'required',
                    'string',
                    'max:200'
                ],
                'announcement_type' => [
                    'required',
                    'string',
                    Rule::in(['announcement', 'update']),
                ],
                'announcement_date' => [
                    'required',
                    'date_format:Y-m-d',
                ],
                'announcement_content' => [
                    'nullable',
                    'string',
                    'max:2000'
                ],
                'user_id' => [
                    'required',
                    'integer',
                    'exists:users'
                ],
                'announcement_file' => [
                    'nullable',
                    'file',
                    'max:20000'
                ],
            ],
            'attributes' => [
                'announcement_title' => '標題',
                'announcement_type' => '公告類別',
                'announcement_date' => '發佈日期',
                'announcement_content' => '內容',
                'user_id' => '承辦人',
                'announcement_file' => '附件下載',
            ]
        ];
    }

    public static function permission($status, $id = null) {
        $pass = true;

        $auth = SessionUtil::getLoginUser();
        if($auth['id'] !== 1) $pass = false;

        return $pass;
    }

    public static function getData($request, $id = null) {
        $query = new _MODEL();
        $auth = SessionUtil::getLoginUser();
        $models = [];
        $collect = collect([]);

        $models = $query->get();
        foreach ($models as $model) {
            $announcement = $model->toArray();
            $user_MODEL = $model->user()->first();
            $user = $user_MODEL->toArray();
            $util = $user_MODEL->util()->first()->toArray();

            $announcement["user_id"] = $user["user_id"];
            $announcement["user_name"] = $user["name"];
            $announcement["util_id"] = $util["util_id"];
            $announcement["util_name"] = $util["util_name"];

            if($auth["id"] === 1) {
                $announcement["editable"] = true;
                $announcement["deletable"] = true;
            } else {
                $announcement["editable"] = false;
                $announcement["deletable"] = false;
            }
            $collect->add($announcement);
        }

        return $collect->toArray();
    }
}
