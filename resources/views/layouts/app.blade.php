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

        <link rel="shortcut icon" href="{{ asset('images/MRG-globle.png') }}" type="image/png" />
        
    </head>
    <body class="momentum-app-layout">
        <header class="shadow-2">
            <div class="header-items" >
                <div class="item header-items--location" >
                    @yield('title')
                </div>
                <a class="item header-items--help" target="_blank">
                    <i class="material-icons">info</i>Help
                </a>
                <a class="item" target="_blank">
                    My LEA 360 Results
                </a>
            </div>
            <div class="momentum-app-width row">
            {{--
            <button class="toggle-side-bar">
            <img height="45px" width="45px" src="{{ asset('images/toggle-side-bar.svg') }}" alt="toggle navigation">
            </button>
            --}}
            </div>
        </header>
        <main class="momentum-app-width row">
            <nav class="side-bar shadow-5">
                <div class="logo">
                    <img
                        src="{{ asset('images/MRG-logo-White.png') }}"
                        srcset="{{ asset('images/MRG-logo-White.png') }} 1x, {{ asset('images/MRG-logo-White.png') }} 2x, {{ asset('images/MRG-logo-White.png') }} 3x" alt="MRG Momentum">
                    <span>&nbsp;|&nbsp;Momentum</span>
                </div>
                <a class="nav-icon {{ active(['dashboard']) }}" href="{{ route('dashboard') }}">
                    <i class="material-icons">home</i>
                    <span 
                        data-culture-key="global.nav.dashboard">
                        @lang('global.nav.dashboard')
                    </span>
                </a>
                <a 
                    class="nav-icon {{ active(['action-plans','action-plans/*']) }}"
                    href="{{ route('action-plans.index') }}">
                    <i class="material-icons">assignment</i>
                    <span 
                        data-culture-key="global.nav.action_plans">
                        @lang('global.nav.action_plans')
                    </span>
                </a>
                <a
                    class="nav-icon {{ active(['pulse-surveys','pulse-surveys/*']) }}"
                    href="{{ route('pulse-surveys.index') }}">
                    <i class="material-icons">assessment</i>
                    <span
                        data-culture-key="global.nav.pulse_surveys">
                        @lang('global.nav.pulse_surveys')
                    </span>
                </a>
                <a 
                    class="nav-icon {{ active(['journal','journal/*']) }}" 
                    href="{{ route('journal') }}">
                    <i class="material-icons">comment</i>
                    <span 
                        data-culture-key="global.nav.journal">
                        @lang('global.nav.journal')
                    </span>
                </a>
                <a 
                    class="nav-icon {{ active(['profile','profile/*']) }}" 
                    href="{{ route('profile') }}">
                    <i class="material-icons">perm_identity</i>
                    <span 
                        data-culture-key="global.nav.profile">
                            @lang('global.nav.profile')
                    </span>
                </a>
                <div class="sign-out-link" >
                    <a 
                        class="nav-icon" 
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <i class="material-icons">power_settings_new</i>
                            <span data-culture-key="global.link.sign_out">
                                @lang('global.link.sign_out')
                            </span>
                </a>
                    <form id="logout-form" action="{{ route('auth.logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </div>
            </nav>
            <div class="center-content col column">
                @yield('content')
                @include('partials.footer')
            </div>
        </main>
        <!-- Scripts -->
        @stack('before_scripts')
        <script src="{{ asset('js/messages.js') }}"></script>
        <script>
        var user = {!! \Auth::user()->load('organization', 'billing_organization')->toJson()!!};
        var behaviors = {!! \Momentum\Behavior::getForFrontend() !!};
        var cultures = {!! \Momentum\Culture::getForFrontend()!!};
        var frontend_config = {!! Momentum\Utilities\Config::getConfigForFrontend() !!};

        @if(isset($data))
            var data = {!! $data !!};
        @endif

        </script>
        <script src="{{ asset('js/manifest.js') }}"></script>
        <script src="{{ asset('js/vendor.js') }}"></script>
        <script src="{{ asset('js/momentum.js') }}"></script>
        @stack('after_scripts')
    </body>
</html>
