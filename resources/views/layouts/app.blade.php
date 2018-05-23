<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    @includeIf('google.analytics')
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{env('APP_NAME')}}</title>

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('/css/main.css') }}" rel="stylesheet">
    <script defer src="https://use.fontawesome.com/releases/v5.0.6/js/all.js"></script>
    <link rel="shortcut icon" href="{{ asset('/images/favicon.ico') }}" type="image/x-icon">
</head>
<body style="background-attachment:fixed;background-color:#35383D;background-size:cover;" background="{{ asset('/images/nebula.jpg') }}">
    <div id="app">
        <nav class="navbar navbar-default navbar-static-top">
            <div class="container">
                <div class="navbar-header">

                    <!-- Collapsed Hamburger -->
                    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse" aria-expanded="false">
                        <span class="sr-only">Toggle Navigation</span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>

                    <!-- Branding Image -->
                    <a href="{{ url('/') }}">
                    <img class="navbar-brand" src="{{ asset('/images/brand.png') }}" alt="Khan Dynasty">
                    </a>
                </div>

                <div class="collapse navbar-collapse" id="app-navbar-collapse">
                    <!-- Left Side Of Navbar -->
                    <ul class="nav navbar-nav">

                      <li><a href="{{ route('demo') }}">Demo View</a></li>
                      <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Tools <span class="caret"></span></a>
                          <ul class="dropdown-menu">
                            <li><a href="https://eveskillboard.com" target="_blank">EveSkillboard</a></li>
                          </ul>
                      </li>
                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="nav navbar-nav navbar-right">
                        <!-- Authentication Links -->
                        @guest
                            <li><a href="{{ route('login') }}">Login</a></li>
                            @unless(env('DISABLE_REGISTER'))
                            <li><a href="{{ route('register') }}">Register</a></li>
                            @endunless
                        @else
                            <li><a href="{{ route('home') }}">Home</a></li>
                            <li><a href="{{ route('extraction') }}">Public Sites</a></li>
                            <li><a href="{{ route('notifications') }}">Notifications</a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" aria-haspopup="true">
                                    {{ Auth::user()->name }} <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu">
                                    <li>
                                        <a href="{{ route('logout') }}"
                                            onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                            Logout
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                            {{ csrf_field() }}
                                        </form>
                                    </li>
                                    <li>
                                      <a href="#" data-toggle="modal" data-target="#delete_account" >Delete Account</a>
                                    </li>
                                </ul>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        @yield('content')
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" src="{{ asset('/js/functions.js') }}"></script>
    <script src="https://use.fontawesome.com/822abcf540.js"></script>
</body>
</html>
