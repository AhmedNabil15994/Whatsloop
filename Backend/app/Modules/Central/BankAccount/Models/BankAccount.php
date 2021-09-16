<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model{

    use \TraitsFunc;

    protected $table = 'bank_accounts';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function getPhotoPath($id, $photo) {
        return \ImagesHelper::GetImagePath('bankAccounts', $id, $photo,false);
    }

    static function dataList($status=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input,$status) {
                    if (isset($input['bank_name']) && !empty($input['bank_name'])) {
                        $query->where('bank_name', 'LIKE', '%' . $input['bank_name'] . '%');
                    } 
                    if (isset($input['account_name']) && !empty($input['account_name'])) {
                        $query->where('account_name', 'LIKE', '%' . $input['account_name'] . '%');
                    } 
                    if (isset($input['account_number']) && !empty($input['account_number'])) {
                        $query->where('account_number', 'LIKE', '%' . $input['account_number'] . '%');
                    } 
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',  $input['id']);
                    } 

                    if($status != null){
                        $query->where('status',$status);
                    }
                })->orderBy('sort','ASC');

        return self::generateObj($source);
    }

    static function generateObj($source){
        $sourceArr = $source->get();

        $list = [];
        foreach($sourceArr as $key => $value) {
            $list[$key] = new \stdClass();
            $list[$key] = self::getData($value);
        }

        // $data['pagination'] = \Helper::GeneratePagination($sourceArr);
        $data['data'] = $list;

        return $data;
    }

    static function getData($source) {
        $data = new  \stdClass();
        $data->id = $source->id;
        $data->bank_name = $source->bank_name != null ? $source->bank_name : '';
        $data->account_name = $source->account_name != null ? $source->account_name : '';
        $data->account_number = $source->account_number != null ? $source->account_number : '';
        $data->sort = $source->sort;
        $data->status = $source->status;
        $data->statusText = $source->status == 0 ? trans('main.notActive') : trans('main.active');
        $data->photo = self::getPhotoPath($source->id, $source->image);
        $data->photo_name = $source->image;
        $data->photo_size = $data->photo != '' ? \ImagesHelper::getPhotoSize($data->photo) : '';
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
