@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="mb-4">Organization</h4>
            
            @include('organization.partials.details', ["organization" => $organization])
        </div>
    </div>
</div>
@endsection
