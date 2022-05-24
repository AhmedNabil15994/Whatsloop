{{-- Extends layout --}}
@extends('tenant.Layouts.V5.master2')
@section('title',trans('main.settings'))

@section('styles')
<link href="{{ asset('css/icons.css') }}" rel="stylesheet">
<style type="text/css" media="screen">
    .formPayment img.designStyle{
        width: 100%;
        height: 400px;
        border-radius: 5px;
        cursor: pointer;
        transition: all ease-in-out 0.25s;
        -webkit-transition: all ease-in-out 0.25s;
        -moz-transition: all ease-in-out 0.25s;
        -o-transition: all ease-in-out 0.25s;
    }
    .formPayment img.designStyle:hover{
        transform: scale(1.1);
    }
    .formPayment img.designStyle.selected{
        border: 2px solid #333;
    }
    .mb-3{
        margin-bottom: 30px;
    }
    #hneehm .imgs{
        width: 100%;
        height: 400px;
        position: relative;
    }
    #hneehm img{
        height: 100%;
        width: 100%;
    }
    #hneehm .form{
        width: 100%;
        padding: 0;
    }
    #hneehm .details{
        position: absolute;
        top: 0;
        left: 20px;
        z-index: 99999;
        font-size: 20px;
        font-weight: 600;
    }
    .logo{
        width: 100px;
        height: 100px;
        display: block;
        margin: auto;
    }
</style>
@endsection

@section('content')

<div class="apiGuide clearfix">
    <h2 class="title">مقدمة</h2>
    <div class="details">
        <h2 class="titleApi"><img class="logo" src="https://hneehm.com/resources/images/logo.svg" alt="hneehm"></h2>
        <div class="desc">
            يمكنك الآن تهنئة عملائك بطريقتك الخاصة عبر واتس لووب
            <br>
            1- اختر التصميم الافتراضي الذي يناسبك.
            <br>
            2- اضبط اعدادات النص ( اللون وحجم الخط ).
            <br>
            3- اكتب النص الذي ترغب ارساله مع الصورة.
            <br>
            اطلب من عملائك ارسال كلمة ( هنيهم ثم مسافة ثم الاسم ) على رقمك لتصلهم التهنئة باسمهم وبالصورة التي قمت باختيارها.
            <br>
            مثال :
            <br>
            هنيهم أحمد بن عبدالعزيز
        </div>
        <div class="clearfix"></div>
    </div>
    </div>
<form action="{{ URL::current() }}" method="post" accept-charset="utf-8">
    @csrf

    <div class="row">
        <div class="form">
            <div class="col-xs-12">
                <h2 class="title">{{ trans('main.selectDesign') }}</h2>
            </div>
            <div class="formPayment">
                <div class="row mt-3">
                    <div class="col-md-3 mb-3" data-area="1">
                        <img class="designStyle {{ $data->design == 1 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/1.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="2">
                        <img class="designStyle {{ $data->design == 2 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/2.png') }}" alt="">
                    </div>
                    <div class="col-md-3 mb-3" data-area="3">
                        <img class="designStyle {{ $data->design == 3 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/3.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="4">
                        <img class="designStyle {{ $data->design == 4 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/4.png') }}" alt="">
                    </div>
                </div>  
                <div class="row mt-3">
                    <div class="col-md-3 mb-3" data-area="5">
                        <img class="designStyle {{ $data->design == 5 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/5.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="6">
                        <img class="designStyle {{ $data->design == 6 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/6.png') }}" alt="">
                    </div>
                    <div class="col-md-3 mb-3" data-area="7">
                        <img class="designStyle {{ $data->design == 7 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/7.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="8">
                        <img class="designStyle {{ $data->design == 8 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/8.png') }}" alt="">
                    </div>
                </div>  
                <div class="row mt-3">
                    <div class="col-md-3 mb-3" data-area="9">
                        <img class="designStyle {{ $data->design == 9 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/9.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="10">
                        <img class="designStyle {{ $data->design == 10 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/10.png') }}" alt="">
                    </div>
                    <div class="col-md-3 mb-3" data-area="11">
                        <img class="designStyle {{ $data->design == 11 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/11.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="12">
                        <img class="designStyle {{ $data->design == 12 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/12.png') }}" alt="">
                    </div>
                </div>  
                <div class="row mt-3">
                    <div class="col-md-3 mb-3" data-area="13">
                        <img class="designStyle {{ $data->design == 13 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/13.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="14">
                        <img class="designStyle {{ $data->design == 14 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/14.png') }}" alt="">
                    </div>
                    <div class="col-md-3 mb-3" data-area="15">
                        <img class="designStyle {{ $data->design == 15 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/15.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="16">
                        <img class="designStyle {{ $data->design == 16 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/16.png') }}" alt="">
                    </div>
                </div>  
                <div class="row mt-3">
                    <div class="col-md-3 mb-3" data-area="17">
                        <img class="designStyle {{ $data->design == 17 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/17.png') }}" alt="">
                    </div>
                    <div class="col-md-2 mb-3" data-area="18">
                        <img class="designStyle {{ $data->design == 18 ? 'selected' : '' }}" src="{{ asset('V5/bnrs/18.png') }}" alt="">
                    </div>
                </div>  
                <input type="hidden" name="design">

            </div>
        </div>
    </div>

    <div class="row">
        <div class="form">
            <div class="col-xs-12">
                <h2 class="title">{{ trans('main.settings') }}</h2>
            </div>
            <div class="formPayment">
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label class="titleLabel">لون الخط :</label>
                    </div>
                    <div class="col-md-9">
                        <input name="color" type="color" value="{{ $data->color != null ? $data->color : '#fff' }}" placeholder="لون الخط">
                    </div>
                </div>  
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label class="titleLabel">حجم الخط :</label>
                    </div>
                    <div class="col-md-9">
                        <input name="size" value="{{ $data->size != null ? $data->size : 30 }}" placeholder="حجم الخط">
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <label class="titleLabel">نص اضافي :</label>
                    </div>
                    <div class="col-md-9">
                        <input name="extra_msg" value="{{$data->extra_msg}}" placeholder="نص اضافي">
                    </div>
                </div>  
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12 text-right">
            <div class="nextPrev clearfix ">
                <button name="Submit" type="submit" class="btnNext AddBTN" id="SubmitBTN">{{ trans('main.apply') }}</button>
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</form>

@section('modals')
<div class="modal fade" id="hneehm" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
    <div class="modal-dialog" role="document" style="width: auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">هنيهم</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form">
                    <div class="row" style="margin: 0;">
                        <div class="form-group">
                            <label class="col-3 col-form-label">مكان النص على الصورة (العرض بالبكسل) :</label>
                            <div class="col-9">
                                <input type="number" min="0" class="form-control" name="margin_left" value="" placeholder="مكان النص على الصورة (العرض بالبكسل)">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-3 col-form-label"> مكان النص على الصورة (الارتفاع بالبكسل) :</label>
                            <div class="col-9">
                                <input type="number" min="0" class="form-control" name="margin_top" value="" placeholder="مكان النص على الصورة (الارتفاع بالبكسل)">
                            </div>
                        </div>
                    </div>  
                    <div class="row mb-3">
                        <div class="imgs">
                            <img src="{{ asset('V5/bnrs/4.png') }}" alt="">
                            <div class="details">
                                الاسم هنا
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success assignTemp">{{ trans('main.save') }}</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">{{ trans('main.back') }}</button>
            </div>
        </div>
    </div>
</div>
@endsection

@include('tenant.Partials.pagination')

@section('scripts')
<script>
    $(function(){
        $('img.designStyle').on('click',function(){
            var imgSrc = $(this).attr('src');
            var imgId = $(this).parent('div.mb-3').data('area');
            $('input[name="design"]').val(imgId);
            $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
            $.ajax({
                type: 'GET',
                url: '/hneehm/getImageDimensions',
                data:{
                    '_token': $('meta[name="csrf-token"]').attr('content'),
                    'image': $('#hneehm img').attr('src'),
                },
                success:function(data){
                    $('#hneehm .modal-dialog').css('width',data[0]);
                    $('#hneehm .imgs').css('width',data[0]);
                    $('#hneehm .imgs').css('height',data[1]);
                    $('#hneehm img').attr('src',imgSrc);
                    setTimeout(function(){
                        $('#hneehm .assignTemp').data('area',imgId);
                        $('#hneehm').modal('toggle');
                    },500);
                },
            });
        });

        $('#hneehm input[name="margin_left"]').keyup(function(){
            var newVal = parseInt($(this).val());
            if(newVal>0){
                var newOffset = parseInt(20 + newVal);
            }else{
                var newOffset = 20;
            }
            $('#hneehm .details').css('left',newOffset+'px');
        });

        $('#hneehm input[name="margin_top"]').keyup(function(){
            var newVal = parseInt($(this).val());
            if(newVal>0){
                var newOffset = parseInt(0 + newVal);
            }else{
                var newOffset = 0;
            }
            $('#hneehm .details').css('top',newOffset+'px');
        });

        $(document).on('click','#hneehm .assignTemp',function(e){
            e.preventDefault();
            e.stopPropagation();
            
            var imgId = $(this).data('area');
            var width = $('#hneehm input[name="margin_left"]').val();
            var height = $('#hneehm input[name="margin_top"]').val();
            
            if(imgId && width > 0 && height > 0){
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                $.ajax({
                    type: 'post',
                    url: '/hneehm/postImageDimensions',
                    data:{
                        '_token': $('meta[name="csrf-token"]').attr('content'),
                        'imgId': imgId,
                        'width' : width,
                        'height' : height,
                    },
                    success:function(data){
                        if(data == 1){
                            $('#hneehm').modal('hide');
                        }
                    },
                });
            }
        });
    });
</script>
@endsection

@endsection
