<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocaleController extends Controller
{
    /**
     * Update the Locale preference.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLocale(Request $request)
    {
        // If the locale exists
        if (array_key_exists($request->input('locale'), config('app.locales'))) {
            // Update the user's locale
            if(Auth::check()) {
                $request->user()->locale = $request->input('locale');
                $request->user()->save();
            }
        }

        return redirect()->back()->withCookie('locale', $request->input('locale'), (60 * 24 * 365 * 10));
    }
}


/*

    The LocaleController class you've posted is a Laravel controller responsible for handling the update of a user's locale (language preference). Here's how it works:

    Check if the Requested Locale Exists: The code first checks if the locale requested (received via the locale input from the request) is in the array of allowed locales (config('app.locales')).

    Update the User's Locale if Authenticated: If the requested locale is valid and the user is authenticated, the user's locale field in the database is updated with the new locale value. The save() method is called to persist this change in the database.

    Set a Cookie: Regardless of whether the user is authenticated or the locale is valid, the method redirects the user back to the previous page and sets a cookie named locale with the value of the requested locale. This cookie has an expiration time of 10 years (60 minutes * 24 hours * 365 days * 10 years).

    Note: In the code above, there's a lack of validation for the input locale. If the locale input doesn't exist in config('app.locales'), the cookie will still be set with that value. It would be good to add some validation or handling for when the locale doesn't exist in the configuration. Otherwise, a user might end up with an invalid locale cookie. You might also want to only set the cookie if the locale is valid.

    Here's an example of how you might modify the code to address these issues:

    php
    Copy code
    if (array_key_exists($request->input('locale'), config('app.locales'))) {
        $locale = $request->input('locale');
        if (Auth::check()) {
            $request->user()->locale = $locale;
            $request->user()->save();
        }
        return redirect()->back()->withCookie('locale', $locale, (60 * 24 * 365 * 10));
    }

    return redirect()->back(); // Return without setting the cookie if the locale is invalid
    This version only sets the cookie if the locale is valid, and returns without setting the cookie if the locale is invalid.

*/