@extends('Tenancy.WhatsappOrder.Views.V5.Designs.1.index')
@section('itemCounts',$data->order->products_count)

@section('title','التحويل البنكي')


@section('content')


<form class="formInfo formMargin" method="POST" action="{{ URL::to('/orders/'.$data->order->order_id.'/bankTransfer') }}" enctype="multipart/form-data">
    @csrf
    <label class="titleForm">اسم البنك</label>
    <input type="text" name="bank_name" class="inputStyle" placeholder="اكتب اسم البنك" />
    <label class="titleForm">اسم صاحب الحساب</label>
    <input type="text" name="account_name" class="inputStyle" placeholder="اضف اسم صاحب الحساب" />
    <label class="titleForm">رقم الحساب</label>
    <input type="text" name="account_number" class="inputStyle" placeholder="أضف رقم الحساب البنكي" />
    <label class="titleForm">المبلغ المحول</label>
    <input type="text" readonly value="{{ $data->order->total_after_discount . ' ' . $data->order->products[0]['currency'] }}" class="inputStyle" placeholder="أضف المبلغ المحول" />
    <label class="titleForm">ارفاق الايصال</label>
    <div class="inputStyle mapStyle uploadStyle">
        <input type="text" class="inputStyle" placeholder="ارفاق إيصال التحويل" />
        <label class="upload">
            <input type="file" name="file" />
            <img src="{{ asset('designs/1/images/cloud.png') }}" alt="" />
        </label>
    </div>
    <button class="btnStyle">تأكيد الطلب</button>
</form>
    
  	
    
@endsection

