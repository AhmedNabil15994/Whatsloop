@extends('emailLayouts.default')
@section('title', ' مرحبا ' . $firstName)
@section('body')
    <tbody>
    <tr>
        <td style="font:14px/25px arial; color:#333; padding: 24px 0 35px;">

            <p>{{ $subject }}</p>
            <br />
            {!! $content !!}
            <p>فريق عمل واتس لووب</p>
        </td>
    </tr>
    </tbody>
@stop
