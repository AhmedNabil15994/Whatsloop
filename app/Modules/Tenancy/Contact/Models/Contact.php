<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class Contact extends Model{

    use \TraitsFunc;

    protected $table = 'contacts';
    protected $primaryKey = 'id';
    public $timestamps = false;
    protected $fillable = [
        'name',
        'email',
        'city',
        'country',
        'phone',
        'group_id',
        'lang',
        'notes',
        'sort',
        'status',
        'created_at',
        'created_by',
    ];
    public function Group(){
        return $this->belongsTo('App\Models\GroupNumber','group_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$id=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['name']) && !empty($input['name'])) {
                        $query->where('name', 'LIKE', '%' . $input['name'] . '%');
                    } 
                    if (isset($input['email']) && !empty($input['email'])) {
                        $query->where('email', 'LIKE', '%' . $input['email'] . '%');
                    } 
                    if (isset($input['city']) && !empty($input['city'])) {
                        $query->where('city', 'LIKE', '%' . $input['city'] . '%');
                    } 
                    if (isset($input['country']) && !empty($input['country'])) {
                        $query->where('country', 'LIKE', '%' . $input['country'] . '%');
                    } 
                    if (isset($input['group_id']) && !empty($input['group_id'])) {
                        $query->where('group_id', $input['group_id']);
                    } 
                    if (isset($input['phone']) && !empty($input['phone'])) {
                        $query->where('phone', $input['phone']);
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
                if($status != null){
                    $source->where('status',$status);
                }
        if(Session::has('channel')){
            $source->whereHas('Group',function($groupQuery){
                $groupQuery->where('channel',Session::get('channel'))->orWhere('channel','');
            });
        }
        if($id != null){
            $source->whereNotIn('id',$id);
        }
        $source->orderBy('sort','ASC');
        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->group_id = $source->group_id;
        $data->group = $source->group_id != null ? $source->Group->{'name_'.LANGUAGE_PREF} : '';
        $data->phone = $source->phone;
        $data->phone2 = str_replace('+', '', $source->phone);
        $data->name = $source->name;
        $data->lang = $source->lang;
        $data->langText = $source->lang == 0 ? trans('main.arabic') : trans('main.english');
        $data->notes = $source->notes;
        $data->email = $source->email != null ? $source->email : '';
        $data->city = $source->city != null ? $source->city : '';
        $data->country = $source->country != null ? $source->country : '';
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
