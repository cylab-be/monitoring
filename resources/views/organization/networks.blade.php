@extends('layouts.app')

@section('title', 'Networks')

@section('content')
<div class="container">
    <h1>Networks</h1>


    <table class="table table-striped">
        @foreach($addresses as $address => $server)
        <tr>
            <td>
                {{ $address }}
                <a class="badge badge-primary" href="ssh://{{ $address }}">ssh</a>
                <a class="badge badge-primary" href="http://{{ $address }}">http</a>
            </td>
            <td>
                <a class="text-decoration-none"
                   href="{{ action('ServerController@show', ['server' => $server]) }}">
                    {{ $server->name }}
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection