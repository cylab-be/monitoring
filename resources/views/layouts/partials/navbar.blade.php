<nav class="navbar navbar-expand-md bg-primary navbar-dark">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <i class="fa d-inline fa-lg fa-line-chart"></i> <b>Monitoring</b>
        </a>
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse"
                data-target="#navbar2SupportedContent" aria-controls="navbar2SupportedContent" aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse text-center" id="navbar2SupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route("dashboard") }}">Dashboard</a>
                </li>
            </ul>

            <ul class="navbar-nav ml-auto">
                @guest
                <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
                @if (Route::has('register'))
                <li class="nav-item"><a class="nav-link" href="{{ route('register') }}">Register</a></li>
                @endif
                @else
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="{{ action('OrganizationController@index') }}"
                       id="navbarDropdownTools" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa d-inline fa-lg fa-bookmark-o"></i>&nbsp;My organizations
                    </a>

                    <div class="dropdown-menu" aria-labelledby="navbarDropdownOrganizations">
                        @foreach (Auth::user()->organizations as $organization)
                        <a class="dropdown-item"
                           href="{{ action("OrganizationController@show", ["organization" => $organization]) }}">
                            {{ $organization->name }}
                        </a>
                        @endforeach
                    </div>
                </li>
                <li>
                    <a class="nav-link" href="{{ route('logout') }}"
                       onclick="event.preventDefault();
                                 document.getElementById('logout-form').submit();">
                        Logout
                    </a>

                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                          style="display: none;">
                        {{ csrf_field() }}
                    </form>
                </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>