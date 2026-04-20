@extends('layouts.app')
@section('title', $subnet->name . ' | ' . $organization->name )

@section('content')
<h1>{{ $subnet->name }}</h1>

<p>
    <span class="btn btn-sm btn-primary">
        <i class="fas fa-network-wired"></i>{{ $subnet->address }}/{{ $subnet->mask }}
    </span>
</p>

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
@endsection