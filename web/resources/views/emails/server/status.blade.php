@component('mail::message')
# {{ $change->server()->name }} : status change

Your server "{{ $change->server()->name }}" went to {!! $change->getStatusBadge() !!}

{{ action("ServerController@show", ["server" => $change->server()]) }}


{{ config('app.name') }}
@endcomponent
