<?php

use App\Models\UserProfile;
use App\Models\Users;
use App\Models\ApiAuth;

trait TraitsFunc
{

    public static function loader(){
        return new self();
    }

    public function actionBy($type = 'create'){
        $userId = \Session::has('user_id');

        if ($type === 'create' ){
            $this->fill(['created_at' => DATE_TIME , 'created_by' => $userId]);
        }elseif($type === 'update'){
            $this->fill(['updated_at' => DATE_TIME , 'updated_by' => $userId]);
        }elseif ($type === 'delete'){
            $this->fill(['deleted_at' => DATE_TIME , 'deleted_by' => $userId]);
        }
    }

    public function scopeNotDeleted($query){
        return $query->whereRaw('(deleted_at IS NULL)');
    }

    public function scopeCreator($query){
        return $query->with(
            'Creator:id,first_name,last_name',
            'Updater:id,first_name,last_name'
        );
    }

    public function MainCreator(){
        return $this->belongsTo(UserProfile::class, 'created_by', 'id')
            ->withDefault(function ($user) {
                $user->display_name = '';
                $user->first_name = '';
                $user->last_name = '';
            });
    }

    public function Updater(){
        return $this->belongsTo(UserProfile::class, 'updated_by', 'id')
            ->withDefault(function ($user) {
                $user->display_name = '';
                $user->first_name = '';
                $user->last_name = '';
            });
    }

    public static function parseName($creatorUpdaterObj){
        if($creatorUpdaterObj !== null && $creatorUpdaterObj->first_name !== '') {

            if($creatorUpdaterObj->diaplay_name !== '') {
                return $creatorUpdaterObj->display_name;
            }

            return $creatorUpdaterObj->first_name . ' ' . $creatorUpdaterObj->last_name ;
        }

        return '' ;
    }

    public static function NotFound(){
        $statusObj['status'] = new \stdClass();
        $statusObj['status']->status = 0;
        $statusObj['status']->code = 204;
        $statusObj['status']->message = trans('main.notFound');
        return \Response::json((object) $statusObj);
    }

    public static function ValidationError($validator){
        $statusObj['status'] = new \stdClass();
        $statusObj['status']->status = 0;
        $statusObj['status']->code = 400;
        $statusObj['status']->message = $validator->messages()->first();
        return \Response::json((object) $statusObj);
    }

    public static function ErrorMessage($message = "Error in process, please try again later", $code = 400){
        $statusObj['status'] = new \stdClass();
        $statusObj['status']->status = 0;
        $statusObj['status']->code = $code;
        $statusObj['status']->message = $message;
        return \Response::json((object) $statusObj);
    }

    public static function SuccessMessage($message = 'Data Generated Successfully'){
        $statusObj['status'] = new stdClass();
        $statusObj['status']->status = 1;
        $statusObj['status']->code = 200;
        $statusObj['status']->message = $message;
        return (object) $statusObj['status'];
    }

    public static function SuccessResponse($message = 'Data Generated Successfully'){
        $statusObj['status'] = new stdClass();
        $statusObj['status']->status = 1;
        $statusObj['status']->code = 200;
        $statusObj['status']->message = $message;
        return \Response::json((object) $statusObj);
    }

    public static function LoginResponse($message = 'Data Generated Successfully'){
        $statusObj['status'] = new stdClass();
        $statusObj['status']->status = 1;
        $statusObj['status']->code = 205;
        $statusObj['status']->message = $message;
        return \Response::json((object) $statusObj);
    }

    public static function PublicDDM($dataArr, $withoutLang = false) {
        $list = [];
        foreach($dataArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key]->id = $value->id;
            $list[$key]->title = $withoutLang == true ? $value->title : $value->{'title' . LANGUAGE_PREF};
        }

        $statusObj['data'] = new \stdClass();
        $statusObj['data'] = $list;
        $statusObj['status'] = self::SuccessResponse(trans('main.loadSuccess'));
        return (object) $statusObj;
    }

    public static function exceptionError($exception){
        $data  = new stdClass();
        $data->status = 0;
        $data->code = 500;
        $data->message = $exception->getMessage();
        $data->line = $exception->getLine();
        $data->file = $exception->getFile();
        $data->exception_code = $exception->getCode();
        $data->exception_code = $exception->getCode();
        return $data;
    }

}
