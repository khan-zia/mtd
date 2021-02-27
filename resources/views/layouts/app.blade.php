<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  @include('partials.head')
  @include('partials.styles')
</head>

<body>
  <div id="mtd" class="mtd-app-content-wrapper">
    @include('partials.header')
    @yield('content')
  </div>
  @include('partials.footer')
  @include('partials.scripts')
</body>

</html>