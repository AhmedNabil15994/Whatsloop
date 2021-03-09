@extends('emailLayouts.default')
@section('title', ' Hello ' . $firstName)
@section('body')
    <tbody>
    <tr>
        <td style="font:14px/25px arial; color:#333; padding: 24px 0 35px;">

            <p>This is your code to reset your account:</p>
            <p>Code :  <strong> {{ $code }}</strong></p>

            <br />
            <p>Many thanks,</p>
            <p>Aliensera</p>
        </td>
    </tr>
    </tbody>
@stop
