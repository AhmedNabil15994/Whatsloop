<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Aliensera - Certification</title>
        <link href="{{ asset('/css/bootstrap.min.css')}}" rel="stylesheet">
        <style type="text/css" media="screen">
            @font-face {
                font-family: Cairo;
                src: url("{{ asset('/fonts/Cairo-Regular.ttf') }}") /* TTF file for CSS3 browsers */
            }
            body,
            html {
                background-color: #fff;
                font-family: 'Cairo', sans-serif;
                position: relative;
            }
            p {
                margin: 0 0 2px !important;
            }
             #bannerProp{
                width: 40px;
                height: 890px;
                background-color: #343F80;
            }
            #bannerProp2{
                height: 75px;
            }
            .header-large{
                font-size: 50px;
                font-weight: 600;
                color: #6b5316;
            }

            .light-bold {
                font-weight: 500;
                font-size: 13px;
            }

            p.certifies,
            p.details{
                font-size: 28px;
                color: #6b5316;
            }

            p.name{
                font-size: 40px;
                font-weight: bold;
                letter-spacing: 2px;
                color: #123d4e;
                display: block;
                width: 500px;
                margin: auto !important;
                margin-top: 40px !important;
                margin-bottom: 40px !important;
                border-bottom: 1px solid #DDD;
                padding-bottom: 8px;
                text-transform: capitalize;
            }

            p.course{
                margin-bottom: 0 !important;
            }

            .medium-font{
                font-size: 15px!important;
            }

            .margin-prop{
                margin-bottom: 20px;
            }

            #logoProp {
                width: 85px;
                height: 75px;
            }

            #footerLogo {
                height: 40px;
                margin-right: 10px;
            }

            #hospLogo {
                height: 55px;
            }

            #signLogo {
                width: 350px;
            }

            #smallFont {
                font-size: 60%;
            }

            #specMargin {
                margin-bottom: 10px;
            }

            b.first{
                padding-top: 8px;
                border-top: 1px solid #DDD;
                display: block;
                width: 200px;
                margin: auto;
                margin-bottom: 10px;
                font-size: 24px;
                color: #123d4e;
            }

            b.second{
                font-size: 28px;
                width: 150px;
                color: #6b5316;
            }

            .col-xs-1{
                padding-left: 0;
            }

            .container-fluid{
                border-radius: 5px;
                position: relative;
                height: 890px;
                overflow: hidden;
                position: relative;
            }
            .col-xs-1.text-right{
                padding-right: 0;
                direction: rtl;
            }
            .col-xs-10{
                height: 890px;
            }

            .first-row .footer{
                position: absolute;
                bottom: 70px;
            }
            .row.first{
                position: absolute;
                width: 100%;
                height: 100%;
                left: 0;
                z-index: 1;
                margin: 0;
                color: #FFF;
                border: 0;
                top: 80px;
            }
            .row.first img.image{
                display: block;
                height: 600px;
                width: 600px;
                margin: auto;
                background-repeat: no-repeat;
                background-size: contain;
                opacity: 0.2;
                border: 0;
                color: #FFF;
                border: 0;
            }
            .row.main,
            .row.main2{
                position: fixed;
                background-color: #343F80;
                left: 0;
                /*z-index: 3;*/
                width: 100%;
                height: 40px;
                background-size: contain;
                margin: 0;
            }
            .row.main{
                top: 0;
            }
            .row.main2{
                bottom: 25px;
                background-color: #343F80;
            }
            .row.main .col-xs-12{
                padding: 0;
            }
            .row.last{
                position: absolute;
                z-index: 2;
                margin-right: 1px;
                border: 25px solid #343F80;
                height: 100%;
            }
            div.data{
                height: 100px;
                display: block;
                width: 100%;
                margin-top: 20px;
            }
            div.data img{
                width: 75px;
                height: 75px;
                display: block;
            }
            div.data .col-xs-6.second{
                direction: rtl;
            }
            div.code{
                padding-top: 10px;
                font-weight: bold;
                font-size: 22px;
                color: #6b5316;
            }
        </style>
    </head>
    <body>
        <div class="container-fluid">
            <div class="row first">
                <img class="image" src="{{ URL::to('/images/logo.jpeg') }}" alt="">
            </div>
            <div class="row last">
                <div class="col-xs-1">
                </div>
                <div class="col-xs-10">
                    <div class="row text-center first-row">
                        <div class="col-xs-12 data">
                            <div class="col-xs-6 text-left code">
                                Code: #{{ $code }}
                            </div>
                            <div class="col-xs-6 second">
                                <img src="{{ URL::to('/images/logo.jpeg') }}" alt="">
                                @if($logo != '')
                                <img src="{{ $logo }}" alt="">
                                @endif
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <p class="header-large">Certificate of Completion</p>
                        </div>
                        <div class="col-xs-12">
                            <p class="light-bold certifies">This certifies that</p>
                        </div>
                        <div class="col-xs-12 text-center">
                            <p class="light-bold name"> {{ $student }}</p>
                        </div>
                        <div class="col-xs-12">
                            <p class="details">had completed the necessary</p>
                            <p class="details"> courses of studies and passed</p>
                            <p class="course name">the {{ $course }} Exams</p>
                        </div>
                        <div class="col-xs-12 footer">
                            <div class="col-xs-4">
                                <b class="second">Date</b>
                                <b class="first">{{ $date }}</b>
                            </div>
                            <div class="col-xs-4"></div>
                            <div class="col-xs-4">
                                <b class="second">Provided By</b>
                                <b class="first">{{ $instructor }}</b>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-1 text-right">
                </div>
            </div>
        </div>
    </body>
</html>
