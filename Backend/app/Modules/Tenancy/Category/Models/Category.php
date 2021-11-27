<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Session;

class Category extends Model{

    use \TraitsFunc;

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $fillable = ['id','channel','name_ar','name_en','color_id','labelId','status','created_by','created_at'];    
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($ids=null,$labelIds=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input) {
                    if (isset($input['color_id']) && !empty($input['color_id'])) {
                        $query->where('color_id',$input['color_id']);
                    }
                    if (isset($input['name_ar']) && !empty($input['name_ar'])) {
                        $query->where('name_ar', 'LIKE', '%' . $input['name_ar'] . '%');
                    }
                    if (isset($input['name_en']) && !empty($input['name_en'])) {
                        $query->where('name_en', 'LIKE', '%' . $input['name_en'] . '%');
                    }
                    if (isset($input['from']) && !empty($input['from']) && isset($input['to']) && !empty($input['to'])) {
                        $query->where('created_at','>=', $input['from'].' 00:00:00')->where('created_at','<=',$input['to']. ' 23:59:59');
                    }
                });
        if(isset($input['channel']) && !empty($input['channel'])){
            $source->where('channel',$input['channel']);
        }elseif(Session::has('channelCode')){
            $source->where('channel',Session::get('channelCode'));
        }

        if($ids != null){
            $source->whereIn('id',$ids);
        }

        if($labelIds != null){
            $source->whereIn('labelId',$labelIds);
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
        $extraData = self::getColorData($source->color_id);
        $data->channel = $source->channel;
        $data->color_id = $source->color_id;
        $data->color = '';
        $data->labelClass = 'badge label label-'.$source->color_id;
        $data->name_ar = $source->name_ar;
        $data->name_en = $source->name_en;
        $data->labelId = $source->labelId;
        $data->hexColor = $extraData[1];
        $data->colorName = $extraData[2];
        $data->title = \Session::has('group_id') ? $source->{'name_'.LANGUAGE_PREF} : $source->name_ar;
        $data->whatsappName = self::getName($source->name_ar,$source->name_en);
        $data->status = $source->status;
        $data->sort = $source->sort;
        $data->created_at = \Helper::formatDate($source->created_at);
        return $data;
    }

    static function getName($name_ar,$name_en){
        $fullName= '';
        if(!empty($name_ar)){
            $fullName = $name_ar;
            if(!empty($name_en)){
                $fullName.= ' - '.$name_en;
            }
        }else{
            if(!empty($name_en)){
                $fullName=$name_en;
            }
        }
        return $fullName;
    }

    static function getColorIndex($color){
        $color_id = 1;
        if($color == 'success'){
            $color_id == 1;
        }elseif($color == 'info'){
            $color_id == 2;
        }elseif($color == 'warning'){
            $color_id == 3;
        }elseif($color == 'danger'){
            $color_id == 4;
        }elseif($color == 'primary'){
            $color_id == 5;
        }elseif($color == 'dark'){
            $color_id == 6;
        }
        return $color_id;
    }

    static function getColor($color_id){
        if($color_id == 1){
            $color = trans('main.green');
            $labelClass = 'success';
            $hexColor = '#66ddaa';
            $colorName = 'MediumAquamarine';
        }elseif($color_id == 2){
            $color = trans('main.blue');
            $labelClass = 'info';
            $hexColor = '#00bfff';
            $colorName = 'MayaBlue';
        }elseif($color_id == 3){
            $color = trans('main.yellow');
            $labelClass = 'warning';
            $hexColor = '#ffcc33';
            $colorName = 'Sunglow';
        }elseif($color_id == 4){
            $color = trans('main.red');
            $labelClass = 'danger';
            $hexColor = '#FF9999';
            $colorName = 'MonaLisa';
        }elseif($color_id == 5){
            $color = trans('main.purple');
            $labelClass = 'primary';
            $hexColor = '#E6E6FA';
            $colorName = 'Lavender';
        }elseif($color_id == 6){
            $color = trans('main.black');
            $labelClass = 'dark';
            $hexColor = '#323a46';
            $colorName = 'Nepal';
        }else{
            $color = '';
            $labelClass = '';
            $hexColor = '';
            $colorName = '';
        }
        return [$color,$labelClass,$hexColor,$colorName];
    }

    static function getColors(){
        return [
            ['id' => 1,'color' => '#ff9dff' , 'title' => 'LavenderRose',],
            ['id' => 2,'color' => '#d3a91d' , 'title' => 'Galliano',],
            ['id' => 3,'color' => '#6d7cce' , 'title' => 'MoodyBlue',],
            ['id' => 4,'color' => '#d7e752' , 'title' => 'Manz',],
            ['id' => 5,'color' => '#00d0e2' , 'title' => 'DarkTurquoise',],
            ['id' => 6,'color' => '#ffc5c7' , 'title' => 'Pink',],
            ['id' => 7,'color' => '#93ceac' , 'title' => 'Chinook',],
            ['id' => 8,'color' => '#f74848' , 'title' => 'SunsetOrange',],
            ['id' => 9,'color' => '#00a0f2' , 'title' => 'DeepSkyBlue',],
            ['id' => 10,'color' => '#83e422' , 'title' => 'InchWorm',],
            ['id' => 11,'color' => '#ffaf04' , 'title' => 'Orange',],
            ['id' => 12,'color' => '#b5ebff' , 'title' => 'ColumbiaBlue',],
            ['id' => 14,'color' => '#9368cf' , 'title' => 'Amethyst',],
            ['id' => 15,'color' => '#ff9485' , 'title' => 'MonaLisa',],
            ['id' => 16,'color' => '#64c4ff' , 'title' => 'MayaBlue',],
            ['id' => 17,'color' => '#ffd429' , 'title' => 'Sunglow',],
            ['id' => 18,'color' => '#dfaef0' , 'title' => 'Lavender',],
            ['id' => 19,'color' => '#99b6c1' , 'title' => 'Nepal',],
            ['id' => 20,'color' => '#55ccb3' , 'title' => 'MediumAquamarine',],
        ];
    }

    static function getColorData($hexColor){
        if($hexColor == '#ff9dff' || $hexColor == 1){
            return [1,'#ff9dff','LavenderRose'];
        }elseif($hexColor == '#d3a91d' || $hexColor == 2){
            return [2,'#d3a91d','Galliano'];
        }elseif($hexColor == '#6d7cce' || $hexColor == 3){
            return [3,'#6d7cce','MoodyBlue'];
        }elseif($hexColor == '#d7e752' || $hexColor == 4){
            return [4,'#d7e752','Manz'];
        }elseif($hexColor == '#00d0e2' || $hexColor == 5){
            return [5,'#00d0e2','DarkTurquoise'];
        }elseif($hexColor == '#ffc5c7' || $hexColor == 6){
            return [6,'#ffc5c7','Pink'];
        }elseif($hexColor == '#93ceac' || $hexColor == 7){
            return [7,'#93ceac','Chinook'];
        }elseif($hexColor == '#f74848' || $hexColor == 8){
            return [8,'#f74848','SunsetOrange'];
        }elseif($hexColor == '#00a0f2' || $hexColor == 9){
            return [9,'#00a0f2','DeepSkyBlue'];
        }elseif($hexColor == '#83e422' || $hexColor == 10){
            return [10,'#83e422','InchWorm'];
        }elseif($hexColor == '#ffaf04' || $hexColor == 11){
            return [11,'#ffaf04','Orange'];
        }elseif($hexColor == '#b5ebff' || $hexColor == 12){
            return [12,'#b5ebff','ColumbiaBlue'];
        }elseif($hexColor == '#9ba6ff' || $hexColor == 13){
            return [13,'#9ba6ff',''];
        }elseif($hexColor == '#9368cf' || $hexColor == 14){
            return [14,'#9368cf','Amethyst'];
        }elseif($hexColor == '#ff9485' || $hexColor == 15){
            return [15,'#ff9485','MonaLisa'];
        }elseif($hexColor == '#64c4ff' || $hexColor == 16){
            return [16,'#64c4ff','MayaBlue'];
        }elseif($hexColor == '#ffd429' || $hexColor == 17){
            return [17,'#ffd429','Sunglow'];
        }elseif($hexColor == '#dfaef0' || $hexColor == 18){
            return [18,'#dfaef0','Lavender'];
        }elseif($hexColor == '#99b6c1' || $hexColor == 19){
            return [19,'#99b6c1','Nepal'];
        }else{
            return [20,'#55ccb3','MediumAquamarine'];
        }

    }

    static function newSortIndex(){
        return self::count() + 1;
    }

    static function reformLabelName($name_ar,$name_en){
        $fullName= '';
        if(!empty($name_ar)){
            $fullName = $name_ar;
            if(!empty($name_en)){
                $fullName.= ' - '.$name_en;
            }
        }else{
            if(!empty($name_en)){
                $fullName=$name_en;
            }
        }
        return $fullName;
    }

    static function newCategory($labelObj){
        $labelId = '';
        $labelObj = self::find($labelObj->id);
        $mainWhatsLoopObj = new \MainWhatsLoop();
        $data['name'] = self::reformLabelName($labelObj->name_ar,$labelObj->name_en);
        $addResult = $mainWhatsLoopObj->createLabel($data);
        $result = $addResult->json();
        // dd($result);
        if($result && isset($result['status']) && $result['status']['status'] == 1){
            $labelId = $result['data']['label']['id'];
            $labelObj->labelId = $labelId;
            $labelObj->save();

            if(isset($labelObj->color_id) && !empty($labelObj->color_id)){
                $updateDate['color'] = Category::getColorData($labelObj->color_id)[2];
                $updateDate['labelId'] = $labelObj->labelId;
                $updateResult = $mainWhatsLoopObj->updateLabel($updateDate);
                $result = $updateResult->json();
            }
        }
    }
}
