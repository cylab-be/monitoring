@extends('layouts.app')

@section('content')
    <div class="py-5">
        <div class="container bg-light">
            <div class="row bg-primary">
                <div class="col-md-12">
                    <h3 class="display-5 text-light">Your organizations</h3>
                </div>
            </div>
            <div class="row bg-light">
                <div class="col-md-12">
                    <p class="lead">Join a new organization
                        <br>
                    </p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <form class="form-horizontal" method="POST"
                          action="{{ action('OrganizationController@addOrg') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Organization name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" name="name"
                                       value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                            </div>

                        </div>
                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="row bg-light">
                <div class="col-md-12">
                    <p class="lead">You're a part of :&nbsp;</p>
                </div>
            </div>
            <div class="row bg-light">
                <div class="col-md-12">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Organization</th>
                            <th>Since</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>

                        @foreach ($organizations as $org)
                            <tr>
                                <td>{{ $org->name }}</td>
                                <td>22-02-17</td>
                                <td><a class="text-dark" href="\org\{{ $org->name }}"><i
                                                class="pull-right fa fa-lg fa-cog"></i></a></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
