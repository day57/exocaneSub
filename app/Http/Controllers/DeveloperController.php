<?php

namespace App\Http\Controllers;

use App\Models\Template;
use Illuminate\Http\Request;

class DeveloperController extends Controller
{
    /**
     * Show the Developer index page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('developers.index');
    }

    /**
     * Show the Developer Documents page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function documents(Request $request)
    {
        if ($request->user()) {
            $templates = Template::whereIn('user_id', [0, $request->user()->id])->get();
        } else {
            $templates = Template::global()->get();
        }

        return view('developers.documents.index', ['templates' => $templates]);
    }

    /**
     * Show the Developer Images page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function images()
    {
        return view('developers.images.index');
    }

    /**
     * Show the Developer Chats page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function chats()
    {
        return view('developers.chats.index');
    }

    /**
     * Show the Developer Messages page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function messages()
    {
        return view('developers.messages.index');
    }

    /**
     * Show the Developer Transcriptions page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function transcriptions()
    {
        return view('developers.transcriptions.index');
    }

    /**
     * Show the Developer Account page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function account()
    {
        return view('developers.account.index');
    }
}


/*

    DeveloperController class seems to be a part of a web application catered towards developers or a developer portal inside a bigger application. The controller handles the logic related to displaying various developer-related pages.

    Here's a brief overview of the functions and their responsibilities:

    Namespace & Imports:
    The class is in the App\Http\Controllers namespace and it's importing the necessary classes like Template model and Laravel's Request class.

    Class Definition & Inheritance:
    The DeveloperController class extends the base Controller class, meaning it inherits the functionalities of the base Controller.

    index():
    Displays the main developer index page.

    documents(Request $request):
    If a user is authenticated ($request->user()), this method fetches templates that either belong to the authenticated user or are global (with a user_id of 0). If the user is not authenticated, only global templates are fetched. These templates are then passed to a view named developers.documents.index.

    images():
    Displays a developer page focused on images.

    chats():
    Displays a developer page for chat functionalities.

    messages():
    Presents a developer page for messages.

    transcriptions():
    Displays a developer page related to transcriptions, perhaps for API or service that does voice-to-text transcriptions.

    account():
    Displays the developer account page where a developer can probably see their account details, API keys, or other account-related functionalities.

*/