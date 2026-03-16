@extends('layouts.app')
@section('title', 'Failed jobs')

@section('content')
<div class="container">
    <h1>Failed jobs</h1>

    <table class="table table-striped my-5">
        @foreach($jobs as $job)
        <tr>
            <td>
                <a href="{{ route('failed-jobs.show', ["failed_job" => $job]) }}">
                    #{{ $job->id }}
                </a>
            </td>
            <td>{{ $job->failed_at }}</td>
            <td>{{ $job->payload->displayName }}</td>

            <td class="text-right">
            </td>
        </tr>
        @endforeach
    </table>
</div>
@endsection