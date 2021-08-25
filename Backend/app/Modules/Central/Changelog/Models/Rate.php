<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rate extends Model{

    use \TraitsFunc;

    protected $table = 'rates';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Changelog(){
        return $this->belongsTo('App\Models\Changelog','changelog_id');
    }

    public function User(){
        return $this->belongsTo('App\Models\CentralUser','user_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList() {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['changelog_id']) && !empty($input['changelog_id'])) {
                        $query->where('changelog_id',  $input['changelog_id']);
                    } 
                    if (isset($input['user_id']) && !empty($input['user_id'])) {
                        $query->where('user_id',  $input['user_id']);
                    } 
                    if (isset($input['tenant_id']) && !empty($input['tenant_id'])) {
                        $query->where('tenant_id',  $input['tenant_id']);
                    } 
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
        $source->orderBy('id','DESC');
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
        $data->changelog_id = $source->changelog_id;
        $data->changelog = $source->Changelog != null ? $source->Changelog->{'title_'.LANGUAGE_PREF} : '';
        $data->user_id = $source->user_id;
        $data->user = $source->User != null ? $source->User->name : '';
        $data->tenant_id = $source->tenant_id;
        $data->rate = $source->rate;
        $data->comment = $source->comment;
        $data->created_at = \Helper::formatDate($source->created_at);
        $data->dateForHuman = \Carbon\Carbon::parse($data->created_at)->diffForHumans();
        return $data;
    }

}
