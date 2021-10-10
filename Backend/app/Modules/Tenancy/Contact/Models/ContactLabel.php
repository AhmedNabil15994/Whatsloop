<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class ContactLabel extends Model{

    use \TraitsFunc;

    protected $table = 'contact_labels';
    protected $primaryKey = 'id';
    protected $fillable = ['id','contact','category_id','created_at'];    
    public $timestamps = false;
   
    public function Group(){
        return $this->belongsTo('App\Models\Category','category_id');
    }

    static function getOne($id){
        return self::where('id', $id)
            ->first();
    }

    static function newRecord($contact,$category_id){
        $dataObj = self::where('contact',$contact)->where('category_id',$category_id)->first();
        if($dataObj == null){
            $dataObj = new self;
            $dataObj->contact = $contact;
            $dataObj->category_id = $category_id;
            $dataObj->created_at = date('Y-m-d H:i:s');
            $dataObj->save();
        }
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
        $categoryObj = Category::getData($source->Category);
        $data->id = $source->id;
        $data->category_id = $source->category_id;
        $data->contact = $source->contact;
        $data->category = $categoryObj;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

}
