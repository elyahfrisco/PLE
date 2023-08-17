<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1">
    <meta name="csrf-token"
          content="{{ csrf_token() }}">
    <meta name="robots"
          content="noindex, nofollow" />

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon"
          href="{{ asset('images/logo.ico') }}">

    <!-- Fonts -->
    {{-- <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet"> --}}

    <!-- Styles -->
    <link rel="stylesheet"
          href="{{ mix('css/app.css') . '?' . date('Y-m-d-H') }}">

    <!-- Scripts -->
    @routes
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.26.0/moment.min.js"></script>
    <script src="{{ mix('js/manifest.js') . '?' . date('Y-m-d-H') }}"
            defer></script>
    <script src="{{ mix('js/vendor.js') . '?' . date('Y-m-d-H') }}"
            defer></script>
    <script src="{{ mix('js/app.js') . '?' . date('Y-m-d-H') }}"
            defer></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
</head>

<body class="bg-light sidebar-mini"
      style="height:
    auto;">
    <div class="preloader flex-column justify-content-center align-items-center">
        <img class="animation__shake"
             src="{{ asset('images/logo.ico') }}"
             alt="Les palisirs de l'eau"
             width="60">
    </div>
    @inertia
</body>

</html>
