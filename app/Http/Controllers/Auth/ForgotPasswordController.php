<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Send a reset link to the given user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $this->validateEmail($request);

            // We will send the password reset link to this user. Once we have attempted
            // to send the link, we will examine the response then see the message we
            // need to show to the user. Finally, we'll send out a proper response.
            $response = $this->broker()->sendResetLink(
                $this->credentials($request)
            );

            return $response == Password::RESET_LINK_SENT
                ? $this->sendResetLinkResponse($request, $response)
                : $this->sendResetLinkFailedResponse($request, $response);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}


/*

    The given PHP code is part of a Laravel application and defines the ForgotPasswordController class responsible for handling password reset requests. Let's dive into the details:

    Namespace and Imports
    Namespace: The namespace is App\Http\Controllers\Auth, which places this class in the Authentication section of your application.
    Imports: The code imports the Controller class, SendsPasswordResetEmails trait, Request class, and Password facade.
    ForgotPasswordController Class
    This class extends Laravel's base Controller class and uses a trait called SendsPasswordResetEmails, which provides common functionality for sending password reset emails.

    Methods
    __construct: Constructor method.

    Applies the 'guest' middleware, meaning that only guests (i.e., users who are not authenticated) can access the methods in this controller.
    sendResetLinkEmail: Sends a password reset link to the given user.

    Validates the email: Uses $this->validateEmail($request) to ensure that the email is valid.
    Sends the reset link: Calls the sendResetLink method on the broker, passing the user's email credentials.
    Checks the response: Compares the response with Password::RESET_LINK_SENT to determine success or failure.
    Returns a response: Depending on success or failure, returns a redirect response or JSON response by calling either $this->sendResetLinkResponse or $this->sendResetLinkFailedResponse.
    Error Handling: If an exception occurs, it redirects back with the error message.
    Trait: SendsPasswordResetEmails
    This trait is a part of Laravel's core, and it provides several methods that assist with sending password reset emails. The methods include:

    validateEmail: Validates the email address in the request.
    broker: Gets the broker to be used during password reset.
    credentials: Gets the credentials to send to the user.
    sendResetLinkResponse: Sends a successful response.
    sendResetLinkFailedResponse: Sends a failure response.
    Conclusion
    The ForgotPasswordController is responsible for managing the process of sending password reset links to users who have forgotten their passwords. It leverages Laravel's built-in functionality for validation, sending emails, and handling responses. This ensures a smooth and secure process for users to reset their passwords when needed.

*/