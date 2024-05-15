@component('mail::message')
# {{ $change->server->organization->name }} / {{ $change->server->name }}

Your server **{{ $change->server->organization->name }} / {{ $change->server->name }}**
went **{{ $change->status() }}**

@component('mail::button', ['url' => action("ServerController@show", ["server" => $change->server])])
    Inspect
@endcomponent

@endcomponent
