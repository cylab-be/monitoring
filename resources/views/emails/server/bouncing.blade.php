@component('mail::message')
# {{ $change->server->organization->name }} / {{ $change->server->name }}

Your server **{{ $change->server->organization->name }} / {{ $change->server->name }}**
seems to be **bouncing** between different states.

This is our last email for today...

@component('mail::button', ['url' => action("ServerController@show", ["server" => $change->server])])
    Inspect
@endcomponent

@endcomponent
