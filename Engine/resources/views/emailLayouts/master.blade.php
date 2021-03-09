@extends('emailLayouts.default')
@section('title', '')
@section('body')
    <tbody>
    <tr>
        <td style="font:14px/25px arial; color:#333; padding: 24px 0 35px;">
            {!! $content !!}
        </td>
    </tr>
    </tbody>
@stop
