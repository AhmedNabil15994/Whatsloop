{{-- Extends layout --}}
@extends('central.Layouts.master')
@section('title',$data->designElems['mainData']['title'])

@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <h4 class="header-title"><i class="{{ $data->designElems['mainData']['icon'] }}"></i> {{ $data->designElems['mainData']['title'] }}</h4>
                        </div>
                    </div>
                    <hr>
                    <form class="form-horizontal" method="POST" action="{{ URL::to('/'.$data->designElems['mainData']['url'].'/update/'.$data->data->oauth_id) }}">
                        @csrf
                        <div class="form-group row mb-3">
                            <label for="inputEmail3" class="col-3 col-form-label">{{ trans('main.client') }} :</label>
                            <div class="col-9">
                                <select class="form-control" data-toggle="select2" name="user_id">
                                    <option>{{ trans('main.choose') }}</option>
                                    @foreach($data->users as $user)
                                    <option value="{{ $user->id }}" {{ $data->data->id == $user->id ? 'selected' : '' }} data-dom="{{ $user->domain }}">{{ $user->channel . ' - ' . $user->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">Webhook URL :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->webhook_url }}" name="webhook_url" disabled placeholder="Webhook URL">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">Client ID :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->client_id }}" name="client_id" placeholder="Client ID">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">Client Secret :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->client_secret }}" name="client_secret" placeholder="Client Secret">
                            </div>
                        </div>
                        <div class="form-group row mb-3">
                            <label for="inputPassword3" class="col-3 col-form-label">Webhook Secret :</label>
                            <div class="col-9">
                                <input type="text" class="form-control" value="{{ $data->data->webhook_secret }}" name="webhook_secret" placeholder="Webhook Secret">
                            </div>
                        </div>
                        <div class="form-group mb-0 justify-content-end row">
                            <div class="col-9">
                                <button name="Submit" type="submit" class="btn btn-success AddBTN" id="SubmitBTN">{{ trans('main.edit') }}</button>
                                <a href="{{ URL::to('/'.$data->designElems['mainData']['url']) }}" type="reset" class="btn btn-danger Reset">{{ trans('main.back') }}</a>
                            </div>
                        </div>
                    </form>
                    <!--end: Datatable-->
                </div> <!-- end card body-->
            </div> <!-- end card -->
        </div><!-- end col-->
    </div>
    <!-- end row-->
</div> <!-- container -->
@endsection

@section('scripts')
<script>
    $(function(){
        $('select[name="user_id"]').on('change',function(){
            $('input[name="webhook_url"]').val('https://'+$(this).children('option:selected').data('dom')+'.wloop.net/whatsloop/webhooks/salla-webhook');
        });
    });
</script>
@endsection