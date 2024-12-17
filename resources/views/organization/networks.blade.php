@extends('layouts.app')

@section('title', 'Networks')

@section('content')
<div class="container">
    <h1>Networks</h1>

    <p>
        @foreach ($networks as $subnet => $addresses)
        <a href="#subnet-{{ $subnet }}" class="btn btn-primary btn-sm mt-1">{{ $subnet }}</a>
        @endforeach
    </p>

    @foreach ($networks as $subnet => $addresses)
        <h3 id="subnet-{{ $subnet }}"
            class="mt-4">
            {{ $subnet }}
        </h3>
        <table class="table table-striped table-sm">
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
    @endforeach
</div>
@endsection