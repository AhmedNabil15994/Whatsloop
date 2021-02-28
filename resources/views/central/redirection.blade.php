@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Rdirection') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{route('central.redirection')}}">
                        @csrf
                        <select class="form-contro" name="tenant_id">
                            <option selected>please choose tenat</option>
                            @foreach($tenats as $tenant)
                                <option value="{{$tenant->id}}">{{$tenant->title}}</option>
                            @endforeach
                        </select>
                        @error('tenant_id')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                        <button type="submit" class="btn btn-primary">submit</button>
                    </form>


                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
