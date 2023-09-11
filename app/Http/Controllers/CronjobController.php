<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProcessCronjobRequest;
use App\Models\Setting;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;

class CronjobController extends Controller
{
    /**
     * Run the scheduled cron job commands.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(ProcessCronjobRequest $request)
    {
        ini_set('max_execution_time', 0);

        Artisan::call('schedule:run');

        Setting::where('name', 'cronjob_executed_at')->update(['value' => Carbon::now()->timestamp]);

        return response()->json([
            'status' => 200
        ], 200);
    }
}



/*

    This code defines a CronjobController within a Laravel application, and it's specifically tasked with running the scheduled cron job commands. Let's break down what's happening in the code:

    Namespace & Imports:

    The namespace App\Http\Controllers defines the location of this class within the project.
    Various classes are imported, such as ProcessCronjobRequest, Setting, Carbon, and Artisan, which are necessary for the logic inside the controller.
    Class Definition & Inheritance:

    php
    Copy code
    class CronjobController extends Controller
    The class is extending the base Controller class, which means it has access to the methods and properties defined in that base class (such as validation, authorization, etc.).

    Method Definition - index:
    This method is responsible for running the scheduled cron job commands in the application.

    Increasing the Execution Time: ini_set('max_execution_time', 0); removes the maximum execution time limit for the script, allowing it to run as long as needed.

    Running the Scheduled Commands: Artisan::call('schedule:run'); uses Laravel's Artisan command-line tool to run the scheduled tasks defined in the application.

    Updating the Execution Timestamp: The Setting model is used to update a record in the database that stores the timestamp of when the cron job was last executed. This can be useful for tracking and logging purposes.

    Returning a Response: Finally, the method returns a JSON response with a 200 status code, which indicates that the request has been processed successfully.

    Usage of ProcessCronjobRequest:
    The method index takes ProcessCronjobRequest as an argument. This likely refers to a form request class that handles the validation of the incoming request. Any validation rules or authorization logic required before running the cron job would be defined in this class.

    Overall, this controller provides a means to execute scheduled tasks within a Laravel application, possibly through an API endpoint or a specific route that could be triggered manually or by a system scheduler. By keeping track of the last execution time and providing a specific endpoint for this purpose, the controller helps in managing and automating regular tasks within the application.

*/