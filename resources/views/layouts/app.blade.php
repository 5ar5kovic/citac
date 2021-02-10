<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>МЗ Малча</title>

    <!-- Scripts -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="node-waves/dist/waves.js" defer></script>
    <script src="/assets/js/app.js" defer></script>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('fontawesome-free/js/all.js') }}" defer></script>
    <script src="js/jquery.dataTables.min.js" defer></script>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="node-waves/dist/waves.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="assets/css/app.css" rel="stylesheet">
    <link href="fontawesome-free/css/all.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/dataTables.bootstrap4.min.css">
    <link rel="icon" type="image/png" href="{{ asset('assets/images/favicon.ico') }}"/>

</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm" style="background-color:#343a40 !important">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}" style="color:#ffffff">
                    МЗ Малча
                </a>
                <button class="navbar-toggler" type="button" style="background-color:#ffffff !important" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav mr-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}" style="color:#ffffff">Пријава</a>
                            </li>
                        @else
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('pretraga') }}" style="color:#ffffff">Претрага</a>
                            </li>
                            @if (Auth::id() == 1)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('home') }}" style="color:#ffffff">Унос</a>
                                </li>
                            @endif
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('ocekivani-prihod') }}" style="color:#ffffff">Очекивани приход</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('izvestaj') }}" style="color:#ffffff">Извештај</a>
                            </li>
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre style="color:#ffffff">
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        Одјава
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>

        <main class="py-4">
            @yield('content')
        </main>
    </div>
</body>
</html>
