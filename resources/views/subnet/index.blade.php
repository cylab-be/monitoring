@extends('layouts.app')
@section('title', 'Subnets | ' . $organization->name )

@section('content')
<div class="container">
    <h1>Subnets</h1>
    <p>
        <a href="{{ route('subnets.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus-circle"></i> New
        </a>
    </p>

    <table class="table table-striped my-5">
        @foreach($organization->subnets->sortBy("name") as $subnet)
        <tr>
            <td>
                <a href="{{ route("subnets.show", ["subnet" => $subnet]) }}"
                   class="text-decoration-none">
                    {{ $subnet->name }}
                </a><br>
                <span class="badge badge-primary">
                    <i class="fas fa-network-wired"></i>
                    {{ $subnet->address }}/{{ $subnet->mask }}
                </span>
                
                <span class="badge badge-primary">
                    {{ $subnet->servers()->count() }} devices
                </span>
            </td>

            <td class="text-right">
                <a class="btn btn-primary btn-sm"
                   href="{{ route('subnets.edit', ['subnet' => $subnet]) }}">
                     Edit
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection