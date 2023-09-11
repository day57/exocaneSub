<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Image;
use App\Models\Template;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Show the Dashboard page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $now = Carbon::now();

        // If the user previously selected a plan
        if (!empty($request->session()->get('plan_redirect'))) {
            return redirect()->route('checkout.index', ['id' => $request->session()->get('plan_redirect')['id'], 'interval' => $request->session()->get('plan_redirect')['interval']]);
        }

        $templates = Template::orderBy('views', 'desc')->limit(6)->get();

        $recentDocuments = Document::with('user', 'template')->where('user_id', $request->user()->id)->orderBy('id', 'desc')->limit(5)->get();
        $recentImages = Image::with('user')->where('user_id', $request->user()->id)->orderBy('id', 'desc')->limit(5)->get();

        return view('dashboard.index', ['now' => $now, 'recentDocuments' => $recentDocuments, 'recentImages' => $recentImages, 'templates' => $templates]);
    }
}


/*

The DashboardController class in the provided code is responsible for handling the logic related to displaying a dashboard view in a Laravel application. Here's a detailed breakdown of what's happening:

Namespace & Imports:
The class is declared in the App\Http\Controllers namespace and imports several classes that are utilized within the method.

Class Definition & Inheritance:
The class DashboardController extends the base Controller class, so it has access to common methods and traits defined in that class.

Method Definition - index:
This method is used to show the dashboard page and perform some logic based on the current user's session and their related data.

Getting the Current Date: $now = Carbon::now(); fetches the current date and time using the Carbon library, which may be used in the view.

Redirecting If a Plan Was Selected: If the user has previously selected a plan (as stored in their session), they are redirected to the checkout page with appropriate parameters. This can be part of a flow where a user needs to complete a purchase or subscription.

Fetching Templates: The top 6 templates ordered by views are fetched from the database.

Fetching Recent Documents and Images: The 5 most recent documents and images related to the authenticated user are retrieved. This includes data from the related user and template models for documents.

Returning the View: Finally, the method returns a view named dashboard.index, passing in the current date and time, recent documents, recent images, and templates as data to be used within the view.

This controller appears to be part of an application that allows users to work with templates, documents, and images, possibly in a content creation or management context. The dashboard view is likely to present an overview of the user's recent activities and popular templates. 

*/