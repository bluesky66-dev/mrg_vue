<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/quasar.mat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/quasar.ie.mat.css') }}" rel="stylesheet">
    <link href="{{ asset('css/material-icons/material-icons.css') }}" rel="stylesheet">
    <link href="{{ asset('css/roboto-font/roboto-font.css') }}" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('images/MRG-globle.png') }}" type="image/png" />
    @stack('style')
</head>
<body class="momentum-app-layout">
    <div class="center-content">
        @php
            $user = Momentum\User::find(app('request')->get('user_id'));
            $client = $user->billing_organization;
            $partner = $user->organization;
        @endphp

        <div class="pdf-header">
            <div style="margin-bottom: 10px;">
            @if($client == $partner)
                <span class="presented-text">{{trans('global.footer.client')}}</span>

                @if($client->logo_path)
                    <img class="organization-logo" src="{{$client->logo_path}}">
                @else
                    <span class="organization-name">{{$client->name}}</span>
                @endif
            @else
                <span class="presented-text">{{trans('global.footer.partner_and_client.prefix')}}</span>
                @if($client->logo_path)
                    <img class="organization-logo" src="{{$client->logo_path}}">
                @else
                    <span class="organization-name">{{$client->name}}</span>
                @endif
                <span class="presented-text">{{trans('global.footer.partner_and_client.for')}}</span>
                @if($partner->logo_path)
                    <img class="organization-logo" src="{{$partner->logo_path}}">
                @else
                    <span class="organization-name">{{$partner->name}}</span>
                @endif
            @endif
            </div>

            <div class="pdf-name">
                <span class="pdf-label">{{trans('global.pdf_header_label.name')}}</span> {{ $user->full_name }}
            </div>
            <div class="pdf-date">
                <span class="pdf-label">{{trans('global.pdf_header_label.date')}}</span> {{ Carbon\Carbon::now()->formatLocalized(config('momentum.long_date_format'))}}
            </div>
        </div>
        <div class="bg-faded hr" style="height: 1px; margin-bottom: 15px;"></div>
        @yield('content')
    </div>
    <script src="{{ asset('js/messages.js') }}"></script>
    <script>
    var cultures = {!! \Momentum\Culture::getForFrontend()!!};

    @if(isset($data))
        var data = {!! $data !!};
    @endif
    </script>
    <script src="{{ asset('js/manifest.js') }}"></script>
    <script src="{{ asset('js/vendor.js') }}"></script>
    <script src="{{ asset('js/momentum.js') }}"></script>
</body>
</html>
