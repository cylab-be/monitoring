<div class="container-fluid p-3  h-100 bg-dark text-white">
    @auth
    <ul class="nav nav-pills flex-column mb-auto" id="nav-main">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle text-white"
               href="{{ action('OrganizationController@index') }}"
               id="navbarDropdownTools" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"
               title="Current organization">
                <i class="d-inline fa fa-bookmark"></i>&nbsp;
                {{ isset($organization) ? $organization->name : 'Select' }}
            </a>

            <div class="dropdown-menu" aria-labelledby="navbarDropdownOrganizations">
                @foreach (Auth::user()->organizations as $current)
                <a class="dropdown-item"
                   href="{{ route("servers.index", ["organization" => $current]) }}">
                    {{ $current->name }}
                </a>
                @endforeach
            </div>
        </li>

        @if (isset($organization) && $organization->exists)
        <li class="nav-item">
            <a class="nav-link text-white"
               href="{{ route("organizations.dashboard", ["organization" => $organization]) }}">
                <i class="fas fa-tachometer-alt w-1-5"></i> Dashboard
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white"
               href="{{ route("servers.index", ["organization" => $organization]) }}">
                <i class="fas fa-desktop w-1-5"></i> Devices
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white"
               href="{{ route("racks.index", ["organization" => $organization]) }}">
                <i class="fas fa-server  w-1-5"></i> Racks
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white"
               href="{{ route("subnets.index", ["organization" => $organization]) }}">
                <i class="fas fa-network-wired w-1-5"></i> Subnets
            </a>
        </li>


        <li class="nav-item">
            <a class="nav-link text-white"
               href="{{ route("tags.index", ["organization" => $organization]) }}">
                <i class="fas fa-tags w-1-5"></i> Tags
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white"
               href="{{ route("insights.packages", ["organization" => $organization]) }}">
                <i class="fas fa-lightbulb w-1-5"></i> Packages
            </a>
        </li>


        @endif
    </ul>

    <ul class="nav nav-pills flex-column mb-0 text-white">
        <hr>

        <li class="nav-item">
            <a class="nav-link text-white" href="/">
                <i class="fa-solid fa-gauge-high w-1-5"></i> Organizations
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route('status') }}">
                Status
            </a>
        </li>

        <li class="nav-item">
            <a class="nav-link text-white" href="{{ route("logout") }}"
               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt w-1-5"></i> Logout
            </a>
        </li>

        <form id="logout-form" action="{{ route("logout") }}" method="POST" style="display: none;">
            @csrf
        </form>
    </ul>
    @endif
</div>