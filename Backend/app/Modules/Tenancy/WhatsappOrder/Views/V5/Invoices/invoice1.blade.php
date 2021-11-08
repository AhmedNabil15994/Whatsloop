@extends('Tenancy.WhatsappOrder.Views.V5.Invoices.index')

@section('content')


<div class="invoce invoice-1">
    <div class="container">
        <div class="invoice-header">
            <h5>فاتورة</h5>
        </div>
        <div class="relative">
            <div class="bill">
                <div class="bill-header">
                    <h5>{{ $data->user->company }}</h5>
                    <p>{{ $data->details->city }}-{{ $data->details->country }}</p>
                    <div class="line"></div>
                </div>
                <div class="qrCode">
                    {!! \QrCode::size(100)->generate(URL::current()) !!}
                </div>
                <div class="bill-list">
                    <ul class="list-unstyled">
                        <li class="itemlist">
                            <h5>الرقم الضريبي</h5>
                            <span>{{ @$data->user->paymentInfo->tax_id }}</span>
                        </li>
                        <li class="itemlist">
                            <h5>السجل التجاري</h5>
                            <span>798985</span>
                        </li>
                        <li class="itemlist">
                            <h5>طريقة الدفع</h5>
                            <span>
                                @if($data->order->payment_type == 1)
                                الدفع عند الاستلام
                                @elseif($data->order->payment_type == 2)
                                الدفع داخل الفرع
                                @elseif($data->order->payment_type == 3)
                                التحويل البنكي
                                @elseif($data->order->payment_type == 4)
                                دفع الكتروني
                                @endif
                            </span>
                        </li>
                        <li class="itemlist">
                            <h5>اسم العميل</h5>
                            <span>{{ $data->details->name }}</span>
                        </li>
                        <li class="itemlist">
                            <h5>سعر المنتجات قبل الضريبة</h5>
                            <span>{{ $data->order->total_after_discount - \Helper::calcTax($data->order->total_after_discount) }} {{ $data->order->products[0]['currency'] }}</span>
                        </li>
                        <li class="itemlist">
                            <h5>قيمة الضريبة</h5>
                            <span>15%</span>
                        </li>
                        <li class="itemlist">
                            <h5>سعر المنتجات مع الضريبة</h5>
                            <span>{{ $data->order->total_after_discount }} {{ $data->order->products[0]['currency'] }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="bill-note">
            <h5>ملاحظات التاجر على الفاتورة</h5>
            <p>يدرج من خلال لوحة التحكم الملاحظات بشكل
            مختصر وبسيط من التاجر للعميل ان وجد
            </p>
        </div>
    </div>
</div>
  	
    
@endsection

