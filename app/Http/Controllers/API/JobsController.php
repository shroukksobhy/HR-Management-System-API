<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Jobs;
class JobsController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve and return a list of jobs
        $jobs = Jobs::all();
        return response()->json($jobs);
    }

    public function show(Request $request, $id)
    {
        // Retrieve and return a specific job
        $job = Job::find($id);
        return response()->json($job);
    }
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'title' => 'required',
            'description' => 'required',
            'location' => 'required',
            'type' => 'required',
            'salary' => 'nullable',
            'company' => 'required',
        ]);

        // Create a new job
        $job = new Jobs;
        $job->title = $request->title;
        $job->description = $request->description;
        $job->location = $request->location;
        $job->type = $request->type;
        $job->salary = $request->salary;
        $job->company = $request->company;
        $job->save();

        return response()->json($job);
    }
}
