<?php namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OldMembership extends Model{

    use \TraitsFunc;

    protected $table = 'old_memberships';
    protected $connection = 'main';
    protected $primaryKey = 'id';
    public $timestamps = false;

    static function getOne($id){
        return self::where('user_id', $id)
            ->first();
    }

    static function calcOldPrice($membershipObj,$duration){
        $price = 0;
        $lastInvoices = Invoice::NotDeleted()->where('client_id',$membershipObj->user_id)->where('main',1)->orderBy('id','DESC')->limit(2)->get();
        $lastInvoices = reset($lastInvoices);
        if(isset($lastInvoices[0]) && !empty($lastInvoices[0]) && $lastInvoices[0]->status != 1 
            && isset($lastInvoices[1]) && !empty($lastInvoices[1]) && $lastInvoices[1]->status != 1){
            return $price;
        }

        if($membershipObj->membership == 'المنصة التفاعلية'){
            $price = 289;
        }elseif($membershipObj->membership == 'باقه البوت'){
            $price = 345;
        }elseif($membershipObj->membership == 'باقه شاملة' || $membershipObj->membership == 'باقة خدمة عملاء واتس لوب'){
            $price = 749;
        }elseif($membershipObj->membership == 'باقه زد'){
            $price = 345;
        }elseif($membershipObj->membership == 'باقة سلة'){
            $price = 345;
        }else{
            $price = 345;
        }
        return $duration == 1 ? $price : $price * 10;
    }

    static function getOldPrice($membership){
        if($membership == 'المنصة التفاعلية'){
            $price = 289;
        }elseif($membership == 'باقه البوت'){
            $price = 345;
        }elseif($membership == 'باقه شاملة' || $membership == 'باقة خدمة عملاء واتس لوب'){
            $price = 749;
        }elseif($membership == 'باقه زد'){
            $price = 345;
        }elseif($membership == 'باقة سلة'){
            $price = 345;
        }else{
            $price = 345;
        }
        return $price;
    }
}
