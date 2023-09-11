<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Template;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the home page.
     *
     * @return \Illuminate\Contracts\Support\Renderable|\Illuminate\Http\RedirectResponse
     */
    public function index()
    {
        // If there's no DB connection setup
        if (!env('DB_DATABASE')) {
            return redirect()->route('install');
        }

        // If the user is logged-in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // If there's a custom site index
        if (config('settings.index')) {
            return redirect()->to(config('settings.index'), 301)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }

        // If there's a payment processor enabled
        if (paymentProcessors()) {
            $plans = Plan::where('visibility', 1)->orderBy('position')->orderBy('id')->get();
        } else {
            $plans = null;
        }

        $templates = Template::global()->premade()->orderBy('name', 'asc')->get();

        $customTemplates = Template::global()->custom()->orderBy('name', 'asc')->get();

        return view('home.index', ['plans' => $plans, 'templates' => $templates, 'customTemplates' => $customTemplates]);
    }
}


/* 

    The HomeController class in this code snippet is responsible for handling the logic related to the home page of a Laravel application. Here's an explanation of what it does:

    __construct()
    The constructor is used to define middleware that the controller should run through. In this case, the middleware line is commented out, so it doesn't affect the request lifecycle.

    index()
    This method handles various scenarios for directing the user when accessing the home page:

    No Database Connection: If the application's environment file doesn't have a database set (DB_DATABASE is not set in the .env file), it redirects the user to the installation route. This can be a handy check if the app requires a database installation step.

    User Already Logged-In: If the user is already logged in, they are redirected to the dashboard.

    Custom Site Index: If there's a custom site index defined in the application's configuration (settings.index), the user is redirected to that URL with a 301 Moved Permanently status, along with cache control headers to prevent caching.

    Payment Processor Check: If there are payment processors enabled (as determined by a helper function paymentProcessors()), it retrieves all visible plans from the Plan model, ordered by position and ID. Otherwise, it sets the plans to null.

    Templates Retrieval: It retrieves all global and premade templates, as well as all global custom templates, from the Template model, ordered by name in ascending order.

    View Rendering: Finally, it returns the home page view (home.index), passing along the plans, templates, and custom templates.

    Summary
    The HomeController class plays an essential role in determining what the user sees when they visit the home page. It checks various conditions such as database connection, authentication status, custom site configuration, and payment processing to decide how to handle the request. It then collects the necessary data and returns the appropriate view or redirection.

    Note: In production code, it is recommended not to use the env() function directly in the code outside of the configuration files, as it can cause issues with configuration caching. Instead, you should access environment variables through the config() function, ensuring that those variables are defined in the corresponding configuration files.

*/