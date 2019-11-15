<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <!-- Google Analytics -->
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', '{{ config('momentum.ga_tracking_id') }}', 'auto');
            ga('send', 'pageview');
        </script>
        <!-- End Google Analytics -->
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
        
    </head>
    <body>
        <div id="momentum-login-layout">
            @yield('content')
        </div>
        <!-- Scripts -->
        @stack('before_scripts')
        <script>
            var cultures = {!! \Momentum\Culture::getForFrontend()!!};
        </script>
        <script src="{{ asset('js/messages.js') }}"></script>
        <script src="{{ asset('js/manifest.js') }}"></script>
        <script src="{{ asset('js/vendor.js') }}"></script>
        <script src="{{ asset('js/login.js') }}"></script>
        @stack('after_scripts')
    </body>
</html>
