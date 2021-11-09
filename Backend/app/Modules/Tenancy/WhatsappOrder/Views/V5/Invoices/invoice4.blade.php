@extends('Tenancy.WhatsappOrder.Views.V5.Invoices.index')

@section('content')
<div class="invoce invoice-2 invoice-4">
    <div class="container">
        <div class="invoice-header">
            <h5>فاتورة</h5>
        </div>
        <div class="personal-profule-parent">
            <div class="person-orifile whats-profile">
                <div class="card-img">
                    <img src="{{ asset('invoices/images/user (1).png') }}" alt="">
                </div>
                <div class="card-body">
                    <p>اسم العميل</p>
                    <h5>{{ $data->details->name }}</h5>
                </div>
            </div>
            <div class="qrcode">
                {!! \QrCode::size(58)->generate(URL::current()) !!}
            </div>
        </div>
        <div class="invoice4-bill">
            <div class="section-profile">
                <div class="card-img">
                    <img src="{{ $data->user->photo }}" alt="">
                </div>
                <div class="card-body">
                    <h5>{{ $data->user->company }}</h5>
                    <p>{{ $data->details->city }}-{{ $data->details->country }}</p>
                </div>
            </div>
            <div class="section-bill-info">
                <ul class="list-unstyled">
                    <li class="listItem">
                        <h5>الرقم الضريبي</h5>
                        <span>{{ @$data->user->paymentInfo->tax_id }}</span>
                    </li>
                    <li class="listItem">
                        <h5>السجل التجاري</h5>
                        <span>798985</span>
                    </li>
                    <li class="listItem">
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
                    <li class="listItem">
                        <h5>سعر المنتجات قبل الضريبة</h5>
                        <span>{{ $data->order->total_after_discount - \Helper::calcTax($data->order->total_after_discount) }} {{ $data->order->products[0]['currency'] }}</span>
                    </li>
                    <li class="listItem">
                        <h5>قيضمة الضريبة</h5>
                        <span>%15</span>
                    </li>
                    <li class="listItem">
                        <h5>سعر المنتجات مع الضريبة</h5>
                        <span>{{ $data->order->total_after_discount }} {{ $data->order->products[0]['currency'] }}</span>
                    </li>
                </ul>
            </div>
            <div class="section-bill-note">
                <h5>ملاحظات التاجر على الفاتورة</h5>
                <p>يدرج من خلال لوحة التحكم الملاحظات بشكل
                مختصر وبسيط من التاجر للعميل ان وجد
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

