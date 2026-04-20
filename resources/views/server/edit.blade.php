@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        @include("server.partials.edit-details")
        
        @if ($server->exists)
        @include("server.partials.manualip")
        
        @include("server.partials.tags")
        @endif
    </div>
</div>
@endsection
