@extends('layouts.app')
@section('title', 'Network Neighbors | ' . $organization->name )

@section('content')
<div class="container">
    <h1>Network Neighbors</h1>

    <div class="mt-4">
        <input autofocus
               class="form-control"
               type="text" id="filter-input"
               placeholder="Search...">
    </div>

    <table class="table table-sm table-striped mt-3"
           id="filter-table">
        <thead>
            <tr>
                <th>IP</th>
                <th>MAC</th>
                <th>Vendor</th>
                <th>Name</th>
                <th>Device</th>
                <th>Seen at</th>
                <th>Seen by</th>
            </tr>
        </thead>
        
        @foreach($neighbors as $neighbor)
        <tr>
            <td>
                {{ $neighbor["ip"] }}
            </td>
            <td>
                {{ $neighbor["mac"] }}
            </td>
            <td>
                {{ $neighbor["vendor"] ?? '' }}
            </td>
            <td>
                {{ $neighbor["name"] ?? '' }}
            </td>
            <td>
                @if($neighbor["known_device"])
                    <a href="{{ $neighbor["known_device"]->url() }}">
                        {{ $neighbor["known_device"]->name }}
                    </a>
                @else
                    <span class="badge badge-secondary">Unknown</span>
                @endif
            </td>
            <td>
                {{ $neighbor["seen_at"] }}
            </td>
            <td>
                <a href="{{ $neighbor["device"]->url() }}">
                    {{ $neighbor["device"]->name }}
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection
