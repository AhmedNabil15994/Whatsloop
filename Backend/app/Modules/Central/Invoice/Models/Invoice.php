<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model{

    use \TraitsFunc;

    protected $table = 'invoices';
    protected $primaryKey = 'id';
    protected $connection = 'main';
    public $timestamps = false;

    public function Client(){
        return $this->belongsTo('App\Models\CentralUser','client_id');
    }

    static function getOne($id){
        return self::NotDeleted()
            ->where('id', $id)
            ->first();
    }

    static function dataList($status=null,$client_id=null) {
        $input = \Request::all();

        $source = self::NotDeleted()->where(function ($query) use ($input,$status,$client_id) { 
                    if (isset($input['id']) && !empty($input['id'])) {
                        $query->where('id',  $input['id']);
                    } 
                    if (isset($input['client_id']) && !empty($input['client_id'])) {
                        $query->where('client_id',  $input['client_id']);
                    } 
                    if (isset($input['status']) && $input['status'] != null) {
                        $query->where('status',  $input['status']);
                    } 
                    if (isset($input['due_date']) && !empty($input['due_date'])) {
                        $query->where('due_date',  $input['due_date']);
                    } 
                    if($status != null){
                        $query->where('status',$status);
                    }
                    if($client_id != null){
                        $query->where('client_id',$client_id);
                    }
                })->orderBy('id','DESC');

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
        $data->client_id = $source->client_id;
        $data->client = $source->Client->name;
        $data->company = $source->Client->company;
        $data->transaction_id = $source->transaction_id;
        $data->due_date = $source->due_date;
        $data->paid_date = $source->paid_date != null ? $source->paid_date : '';
        $data->payment_method = $source->payment_method;
        $data->notes = $source->notes;
        $data->total = $source->total;
        $data->payment_gateaway = $source->payment_gateaway;
        $data->items = $source->items != null ? unserialize($source->items) : [];
        $data->sort = $source->sort;
        $data->status = $source->status;
        $data->statusText = trans('main.invoice_status_'.$source->status);
        $data->created_at = \Helper::formatDateForDisplay($source->created_at,true);
        return $data;
    }

    static function newSortIndex(){
        return self::count() + 1;
    }

}
