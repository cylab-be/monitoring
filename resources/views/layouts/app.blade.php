<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
@include("layouts.partials.head")

<body class="h-100">
    <div class="row h-100">
        <div class="col-xl-2 col-sm-3">
            @include("layouts.partials.navbar")
        </div>

        <div class="col-xl-10 col-sm-9">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>
    </div>
</body>
</html>
