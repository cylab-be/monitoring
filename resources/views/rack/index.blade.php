@extends('layouts.app')
@section('title', 'Racks | ' . $organization->name )

@section('content')
<div class="container">
    <h1>Racks</h1>
    <p>
        <a href="{{ route('racks.create') }}" class="btn btn-primary btn-sm">
            <i class="fa fa-plus-circle"></i> New
        </a>

        <a href="{{ route("racks.dashboard") }}"
            class="btn btn-primary btn-sm">
               <i class="fas fa-search"></i> Front view
        </a>
    </p>

    <table class="table table-striped my-5">
        @foreach($organization->racks->sortBy("name") as $rack)
        <tr>
            <td>
                {{ $rack->name }}
                <br>
                <span class="badge badge-primary">{{ $rack->height }}U</span>
                <span class="badge badge-primary"><i class="fas fa-server"></i> {{ $rack->servers()->count() }} servers</span>
            </td>

            <td class="text-right">
                <a class="btn btn-primary btn-sm"
                   href="{{ route('racks.edit', ['rack' => $rack]) }}">
                     Edit
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection