@extends('Tenancy.WhatsappOrder.Views.V5.Designs.3.index')
@section('itemCounts',$data->order->products_count)

@section('title',$data->user->company)


@section('content')

@if($data->order->payment_type == 4)
 <div id="thanks" class="thanks" role="dialog">
    <div class="col-xs-12 mt-5">                
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-body">
                <center>
                   <img src="{{ asset('designs/3/images/waiting.png') }}" class="fa fa-spin" alt="" />
                   <h2 class="thankU">شكراً لك</h2>
                   <div class="desc">جاري التوجه لبوابة الدفع الالكتروني</div>
               </center>
            </div>
        </div>
    </div>
</div>
<form class="finish_submit" method="get" action="{{ $data->url }}">
    <input type="hidden" name="data" value="{{ $data->urlData }}">
</form>
@section('scripts')
<script>       
    $(function () {
        setTimeout(function(){
            $('.finish_submit').submit();
        },2500);
    });
</script>      
@endsection

@else
<div id="thanks2" class="thanks">
      <div class="col-xs-12 mt-5">
        <div class="modal-content">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <div class="modal-body">
                <center>
                   <img src="{{ asset('designs/3/images/delivery-truck.png') }}" alt="" />
                   <h2 class="thankU">شكراً لك</h2>
                   <div class="desc">
                        تم ارسال طلبكم بنجاح رقم الطلب
                        <span>#{{ $data->order->order_id }}</span>
                   </div>
               </center>
            </div>
        </div>
    </div>
</div>
@endif
    
@endsection

