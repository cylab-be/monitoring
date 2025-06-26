@extends('layouts.app')
@section('title', $tag->name . ' | ' . $organization->name )

@section('content')
<div class="container">
    <h1>{{ $tag->name }}</h1>

    <table class="table table-striped my-5">
        @foreach($tag->servers as $server)
        <tr>
            <td>
                <a href="{{ $server->url() }}"
                   class="text-decoration-none">
                {{ $server->name }}
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection