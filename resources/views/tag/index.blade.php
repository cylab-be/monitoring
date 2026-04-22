@extends('layouts.app')
@section('title', 'Tags | ' . $organization->name )

@section('content')
<div class="container">
    <h1>Tags</h1>
    <p>
        <a href="{{ route('tags.create', ["organization" => $organization]) }}"
           class="btn btn-primary btn-sm">
            <i class="fa fa-plus-circle"></i> New
        </a>
    </p>

    <table class="table table-striped my-5">
        @foreach($organization->tags->sortBy("name") as $tag)
        <tr>
            <td>
                <a href="{{ route("tags.show", ["tag" => $tag]) }}"
                   class="text-decoration-none">
                    {{ $tag->name }}
                </a><br>
                <span class="badge badge-primary">
                    <i class="fas fa-desktop"></i> 
                    {{ $tag->servers()->count() }} devices
                </span>
            </td>

            <td class="text-right">
                <a class="btn btn-primary btn-sm"
                   href="{{ route('tags.edit', ['tag' => $tag]) }}">
                     Edit
                </a>
                
                <form method="POST" 
                      action="{{ route("tags.destroy", ["tag" => $tag]) }}"
                      class="d-inline-block">
                    @csrf
                    @method("DELETE")
                    <button class="btn btn-danger btn-sm">
                        <i class="fa fa-times-circle" aria-hidden="true"></i> Delete
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection