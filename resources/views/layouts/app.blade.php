<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include("layouts.partials.head")

<body>
    @include("layouts.partials.navbar")

    @yield('content')
</body>
</html>
