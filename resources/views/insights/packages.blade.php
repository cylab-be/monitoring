@extends('layouts.app')
@section('title', 'Installed Packages | ' . $organization->name )

@section('content')
<div class="container">
    <h1>Installed Packages</h1>

    <div class="mt-4">
        <input autofocus
               class="form-control"
               type="text" id="filter-input"
               placeholder="Search...">
    </div>

    <table class="table table-sm table-striped mt-3"
           id="filter-table">
        @foreach($packages as $package)
        <tr>
            <td>
                {{ $package["name"] }}
            </td>
            <td>
                <a href="{{ $package["device"]->url() }}">
                    {{ $package["device"]->name }}
                </a>
            </td>
            <td>
                {{ $package["source"] }}
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection