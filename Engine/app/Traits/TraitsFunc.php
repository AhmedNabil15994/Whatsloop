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
        return $query->whereRaw('(deleted_at IS NULL OR deleted_at="0000-00-00 00:00:00")');
    }

	public function scopeCreator($query){
        return $query->with(
            'Creator:id,first_name,last_name',
            'Updater:id,first_name,last_name'
        );
    }

	public function Creator(){
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
        $statusObj['status']->satuts = 0;
        $statusObj['status']->code = 204;
        $statusObj['status']->message = 'This Item Not Found Or Deleted';
        return \Response::json((object) $statusObj);
    }

    public static function NotAllowed(){
        $statusObj['status'] = new \stdClass();
        $statusObj['status']->satuts = 0;
        $statusObj['status']->code = 405;
        $statusObj['status']->message = 'Error in process, Method Not Allowed';
        return \Response::json((object) $statusObj);
    }

	public static function ValidationError($validator){
        $statusObj['status'] = new \stdClass();
        $statusObj['status']->satuts = 0;
        $statusObj['status']->code = 400;
        $statusObj['status']->message = $validator->messages()->first();
        return \Response::json((object) $statusObj);
	}

    public static function ErrorMessage($message = "Error in process, please try again later", $code = 400){     
        $statusObj['status'] = new \stdClass();
        $statusObj['status']->satuts = 0;
        $statusObj['status']->code = $code;
        $statusObj['status']->message = $message;
        return \Response::json((object) $statusObj);
    }
    
	public static function SuccessResponse($message = 'Data Generated Successfully'){
	    $statusObj = new stdClass();
	    $statusObj->status = 1;
	    $statusObj->code = 200;
        $statusObj->message = $message;
		return (object) $statusObj;
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
        $statusObj['status'] = self::SuccessResponse("Load data Success");
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

	private Static function Pagination($object){
		$uri = \Illuminate\Support\Facades\Input::getUri();
		$pageparam = $object->getPageName();
		$currentPage = $object->currentPage() ;
		$lastPage = $object->lastpage() ;
		$data = new \stdClass();
		$data->count = $object->count();
		$data->total_count = $object->total();
		$data->current_page = $object->currentPage();
		$data->last_page = $object->lastpage();
		$next = $currentPage + 1;
		$prev = $lastPage - 1;
		$newUrl = self::paginationFilter($uri, "$pageparam");
		if(preg_match('/(&)/' , $newUrl) != 0 || strpos($newUrl , '?') != false ){
			$separator = '&';
		}else{
			$separator = '?';
		}
		if($currentPage !=  $lastPage ){
			$link = str_replace('&&' , '&' , $newUrl . $separator. "$pageparam=". $next);
			$link = str_replace('?&' , '?' , $link);
			$data->next = $link;
			if($currentPage == 1){
				$data->prev = "";
			}else{
				$link = str_replace('&&' , '&' , $newUrl . $separator. "$pageparam=". $prev);
				$link = str_replace('?&' , '?' , $link);
				$data->prev = $link ;
			}
		}else{
			$data->next = "";
			if($currentPage == 1){
				$data->prev = "";
			}else{
				$link = str_replace('&&' , '&' , $newUrl . $separator. "$pageparam=". $prev);
				$link = str_replace('?&' , '?' , $link);
				$data->prev = $link ;
			}
		}
		return $data;
	}

	private static function paginationFilter($url, $key) {
		if(strpos($key , $url) == false){
			$url = preg_replace('/(.*)(?)' . $key . '=[^&]+?(?)[0-9]{0,4}(.*)|[^&]+&(&)(.*)/i', '$1$2$3$4$5$6$7$8$9$10$11$12$13$14$15$16$17$18$19$20', $url . '&');
			$url = substr($url, 0, -1);
			return $url ;
		}else{
			return $url;
		}
	}

}
