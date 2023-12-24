@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <h4 class="mb-4">Organization</h4>
            
            <div class="card">
                <div class="card-header">Details</div>

                <div class="card-body">
                    @if (!$organization->exists)
                    <form method="POST" action="{{ action("OrganizationController@store") }}">
                    @else
                    <form method="POST"
                          action="{{ action("OrganizationController@update", ["organization" => $organization]) }}">
                    {{ method_field("PUT") }}
                    @endif
                        {{ csrf_field() }}

                        <div class="form-group">
                            <label for="name" >Name</label>

                            <input id="name" type="text"
                                   class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}"
                                   name="name"
                                   value="{{ old('name', $organization->name) }}" required autofocus>

                            @if ($errors->has('name'))
                                <span class="invalid-feedback">
                                    <strong>{{ $errors->first('name') }}</strong>
                                </span>
                            @endif
                        </div>

                        <div class="form-group mt-2">
                            <button type="submit" class="btn btn-primary btn-sm">
                                 Save
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="card mt-3">
                <div class="card-header">Members</div>

                <div class="card-body">
                    <table class="table table-sm">
                        @foreach ($organization->users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td class="text-right">
                                <form action="{{ action("OrganizationUserController@destroy", ["organization" => $organization, "user" => $user]) }}"
                                    method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger btn-sm">Remove</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </table>
                    
                    <p>
                        <a class="btn btn-primary btn-sm"
                           href="{{ action("OrganizationUserController@create", ["organization" => $organization]) }}">
                            Invite user to organization
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
