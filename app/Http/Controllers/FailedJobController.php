<?php

namespace App\Http\Controllers;

use App\FailedJob;

class FailedJobController extends Controller
{
    public function index()
    {
        return view('failed-job.index', [
            'jobs' => FailedJob::orderByDesc('id')->select(['id', 'payload', 'failed_at'])->get()]);
    }
    
    public function show(FailedJob $failed_job)
    {
        return view('failed-job.show', ['job' => $failed_job]);
    }
}
