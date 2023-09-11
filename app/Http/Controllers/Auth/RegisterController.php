<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Str;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

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
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'max:128', 'confirmed'],
            'agreement' => ['required'],
            'g-recaptcha-response' => [(config('settings.captcha_registration') ? 'required' : 'sometimes'), 'captcha']
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User|void
     */
    protected function create(array $data)
    {
        // If the registration is enabled
        if (config('settings.registration')) {
            $user = new User;

            $user->name = $data['name'];
            $user->email = $data['email'];
            $user->password = Hash::make($data['password']);
            $user->locale = app()->getLocale();
            $user->timezone = config('settings.timezone');
            $user->api_token = Str::random(64);
            $user->tfa = config('settings.registration_tfa');
            $user->default_language = config('settings.openai_default_language');

            $user->save();

            if (!config('settings.registration_verification')) {
                $user->markEmailAsVerified();
            }

            return $user;
        }
    }

    /**
     * Show the application registration form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|void
     */
    public function showRegistrationForm(Request $request)
    {
        // If the request comes from the Home or Pricing page, and the user has selected a plan
        if (($request->server('HTTP_REFERER') == route('pricing') || $request->server('HTTP_REFERER') == route('home').'/') && $request->input('plan') > 1) {
            $request->session()->put('plan_redirect', ['id' => $request->input('plan'), 'interval' => $request->input('interval')]);
        }

        // If the registration is enabled
        if (config('settings.registration')) {
            return view('auth.register');
        }

        abort(404);
    }
}



/*
    The given PHP code defines a Laravel controller, RegisterController, responsible for handling user registration in the application. This controller manages user validation, creation, and registration view rendering. Here's an overview of its structure and functionalities:

    Namespace and Imports
    Namespace: App\Http\Controllers\Auth, specifying the location of the class.
    Imports: Various classes and facades are imported, such as User model, Hash, Validator, and Str.
    RegisterController Class
    The RegisterController class leverages Laravel's built-in trait RegistersUsers to simplify registration functionality. It contains the following methods:

    __construct: Constructor method that applies the 'guest' middleware to the class, meaning that only guests (not logged-in users) can access these methods.

    validator: This protected method validates incoming registration requests. It checks:

    name: Required, string, maximum of 255 characters.
    email: Required, string, email format, unique in users table, maximum of 255 characters.
    password: Required, string, minimum of 6 and maximum of 128 characters, and must be confirmed.
    agreement: Required, ensuring that the user has agreed to terms or conditions.
    g-recaptcha-response: Conditional validation based on the configuration; if captcha is enabled on registration, this field is required.
    create: Protected method that creates a new user instance after a valid registration. It checks if registration is enabled and then fills the user model with data from the request, including handling password hashing, generating an API token, and setting various default settings. If email verification is not required, it marks the email as verified.

    showRegistrationForm: Public method that renders the registration form view. Similar to the login controller you provided earlier, it handles logic for redirecting to a plan if the request comes from specific routes. It also checks if registration is enabled; if not, it aborts with a 404 error.

    Other Properties
    redirectTo: A protected variable that defines where to redirect users after registration, set to the '/dashboard' route.
    Conclusion
    The RegisterController class manages user registration by controlling validation, creation, and the display of the registration form. It considers various configurable settings, such as enabling or disabling registration and captcha. This code is a typical example of how user registration is handled in a Laravel application, using the framework's built-in features to achieve a clean and efficient implementation

*/
