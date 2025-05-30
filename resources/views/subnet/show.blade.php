@extends('layouts.app')
@section('title', $subnet->name . ' | ' . $organization->name )

@section('content')
<div class="container">
    <h1>{{ $subnet->name }}</h1>
    <p>{{ $subnet->address }}/{{ $subnet->mask }}</p>

    <table class="table table-striped my-5">
        @foreach($subnet->servers() as [$server, $ip])
        <tr>
            <td>
                <a href="{{ $server->url() }}"
                   class="text-decoration-none">
                {{ $server->name }}
                </a>
            </td>
            <td>
                {{ $ip }}
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection