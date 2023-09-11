<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMailRequest;
use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    /**
     * Show the Contact page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * Send the Contact email.
     *
     * @param ContactMailRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function send(ContactMailRequest $request)
    {
        try {
            Mail::to(config('settings.contact_email'))->send(new ContactMail());
        } catch(\Exception $e) {
            return redirect()->route('contact')->with('error', $e->getMessage());
        }

        return redirect()->route('contact')->with('success', __('Thank you!').' '.__('We\'ve received your message.'));
    }
}



/*

    This code is a simple Laravel controller for handling a contact form functionality. It has two methods, responsible for displaying the contact page and sending the contact email.

    Namespace & Imports:
    The code defines the namespace and imports necessary classes. It’s part of the App\Http\Controllers namespace, and it imports some classes to handle requests and mailing functionality.

    Class Definition:

    php
    Copy code
    class ContactController extends Controller
    The ContactController class extends Laravel's base Controller class.

    Displaying the Contact Page (index method):
    php
    Copy code
    public function index()
    {
        return view('contact.index');
    }
    This method returns a view named 'contact.index', which would likely display a contact form to the user.

    Sending the Contact Email (send method):
    php
    Copy code
    public function send(ContactMailRequest $request)
    {
        try {
            Mail::to(config('settings.contact_email'))->send(new ContactMail());
        } catch(\Exception $e) {
            return redirect()->route('contact')->with('error', $e->getMessage());
        }

        return redirect()->route('contact')->with('success', __('Thank you!').' '.__('We\'ve received your message.'));
    }
    This method handles the process of sending a contact email.

    Input validation: The method uses the ContactMailRequest class, likely a custom request class, to handle input validation.
    Sending email: The code sends an email to the address defined in config('settings.contact_email') using the ContactMail mail class. This class probably contains the logic for constructing the email, such as its subject, body, and possibly attaching data from the contact form.
    Exception Handling: If there's an exception (such as a failure to send the email), the code redirects back to the contact page with an error message containing the exception's message.
    Success Response: If the email is sent successfully, the code redirects back to the contact page with a success message.
    In summary, this controller is responsible for showing a contact page and processing the form submission by sending an email. It uses Laravel's Mail facade, and the process is wrapped in a try-catch block to handle any errors that might occur during the sending process.

*/