@extends('layouts.app')

@section('content')
<div class="container">
    <h1>{{ $server->name }}</h1>
    
    @include("record.table")

</div>
@endsection
