@extends('layouts.app')
@section('title', 'Docker Stacks | ' . $organization->name )

@section('content')
<div class="container">
    <h1>Docker Stacks</h1>

    <div class="mt-4">
        <input autofocus
               class="form-control"
               type="text" id="filter-input"
               placeholder="Search...">
    </div>

    <table class="table table-sm table-striped mt-3"
           id="filter-table">
        @foreach($stacks as $stack)
        <tr>
            <td>
                {{ $stack->Name }}
            </td>
            <td>
                {{ $stack->Status }}
            </td>
            <td>
                <a href="{{ $stack->device->url() }}">
                    {{ $stack->device->name }}
                </a>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection