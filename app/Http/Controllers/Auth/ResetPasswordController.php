<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }
}

/*

    The ResetPasswordController class provided here is responsible for handling password reset requests in a Laravel application. It leverages Laravel's built-in ResetsPasswords trait, which encapsulates the core logic for resetting passwords, making the controller concise and easy to understand.

    Here's an overview of its structure and functionalities:

    Namespace and Imports
    Namespace: App\Http\Controllers\Auth, specifying the location of the class.
    Imports: The base Controller class is imported along with the ResetsPasswords trait.
    ResetPasswordController Class
    The ResetPasswordController class mainly uses Laravel's ResetsPasswords trait, so most of the functionality is derived from that trait. Here are the key components:

    use ResetsPasswords: By including this trait, the controller gains all the necessary methods to handle password reset requests, such as sending reset link emails, resetting the password, and responding to reset requests.

    redirectTo: A protected variable that defines where to redirect users after successfully resetting their password. It's set to the root route ('/').

    __construct: Constructor method that applies the 'guest' middleware to the class, meaning that only guests (not logged-in users) can access these methods.

    Conclusion
    The ResetPasswordController class offers a clean and concise way to handle password resets by using a prebuilt Laravel trait. By including the ResetsPasswords trait, the developer gets out-of-the-box functionality for handling password reset requests, including link generation, validation, and response handling.

    This code follows the convention for password resets in Laravel, and it aligns with typical best practices, leveraging built-in features for robustness and security.

*/