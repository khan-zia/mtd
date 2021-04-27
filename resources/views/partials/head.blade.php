<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

@stack('page-title')

<!-- Scripts -->
<!-- <script src="{{ asset('js/app.js') }}" defer></script> -->

<!-- Fonts -->
<link rel="dns-prefetch" href="//fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

<!-- Styles -->
<link href="{{ asset('css/app.css') }}" rel="stylesheet">

<link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">
<link rel="icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon">