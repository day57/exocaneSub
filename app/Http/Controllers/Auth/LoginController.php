<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\TfaMail;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
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
        $this->middleware('guest')->except('logout');
    }

    /**
     * Show the application's login form.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function showLoginForm(Request $request)
    {
        // If the request comes from the Home or Pricing page, and the user has selected a plan
        if (($request->server('HTTP_REFERER') == route('pricing') || $request->server('HTTP_REFERER') == route('home').'/') && $request->input('plan') > 1) {
            $request->session()->put('plan_redirect', ['id' => $request->input('plan'), 'interval' => $request->input('interval')]);
        }

        if ($request->session()->get('email')) {
            $request->session()->keep(['email', 'remember']);

            return view('auth/tfa');
        }

        return view('auth.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|JsonResponse|\Illuminate\Http\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        $user = User::where($this->username(), '=', $request->input($this->username()))->first();

        // If the user exists, and has two-factor authentication enabled
        if (config('settings.login_tfa') && $user && $user->tfa) {
            // If the user credentials are valid
            if (auth()->validate($this->credentials($request))) {
                try {
                    Mail::to($user->email)->locale($user->locale)->send(new TfaMail($this->resetTfaCode($user)));
                } catch(\Exception $e) {
                    return redirect()->route('login')->with('error', $e->getMessage());
                }

                $request->session()->flash($this->username(), $request->input($this->username()));
                $request->session()->flash('remember', $request->boolean('remember'));

                return view('auth/tfa');
            }
        } else {
            if ($this->attemptLogin($request)) {
                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }

                return $this->sendLoginResponse($request);
            }
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Handle a login request to the application.
     *
     * @param Request $request
     * @return JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function validateTfaCode(Request $request)
    {
        $request->session()->keep(['email', 'remember']);

        $user = User::where($this->username(), '=', $request->session()->get('email'))->first();

        // If the user exists, and has two-factor authentication enabled
        if (config('settings.login_tfa') && $user && $user->tfa) {
            $request->validate([
                'code' => ['required', 'integer',
                    function ($attribute, $value, $fail) use ($user) {
                        if ($value != $user->tfa_code) {
                            $fail(__("The security code is incorrect."));
                        }
                    },
                    function ($attribute, $value, $fail) use ($user) {
                        if ($user->tfa_code_created_at->lt(Carbon::now()->subMinutes(30))) {
                            $fail(__("The security code is expired."));
                        }
                    }
                ]
            ]);

            try {
                auth()->login($user, $request->session()->get('remember'));

                if ($request->hasSession()) {
                    $request->session()->put('auth.password_confirmed_at', time());
                }

                $this->resetTfaCode($user);

                $request->session()->forget(['email', 'remember']);

                return $this->sendLoginResponse($request);
            } catch (\Exception $e) {
                return redirect()->route('login')->with('error', $e->getMessage());
            }
        }

        return redirect()->route('login');
    }

    /**
     * Resends the two-factor authentication code to the user.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function resendTfaCode(Request $request)
    {
        $request->session()->keep(['email', 'remember']);

        $user = User::where($this->username(), '=', $request->session()->get('email'))->first();

        // If the user exists, and has two-factor authentication enabled
        if (config('settings.login_tfa') && $user && $user->tfa) {
            try {
                Mail::to($user->email)->locale($user->locale)->send(new TfaMail($this->resetTfaCode($user)));
            } catch(\Exception $e) {
                return redirect()->route('login')->with('error', $e->getMessage());
            }

            return back()->with('success', __('A new security code has been sent to your email address.'));
        }

        return redirect()->route('login');
    }

    /**
     * Resets the user's two-factor authentication code.
     *
     * @param User $user
     * @return int|mixed
     * @throws \Exception
     */
    private function resetTfaCode(User $user)
    {
        $user->tfa_code = random_int(100000, 999999);
        $user->tfa_code_created_at = Carbon::now();
        $user->save();

        return $user->tfa_code;
    }
}


/*

    The given PHP code is a Laravel controller, LoginController, responsible for managing user authentication in the application. This controller includes functionalities for regular login, displaying the login form, and managing two-factor authentication (TFA). Here's an overview of its components:

    Namespace and Imports
    Namespace: App\Http\Controllers\Auth, specifying the location of the class.
    Imports: Necessary classes and facades are imported, including User model, Carbon for date handling, Mail facade, and a custom mail class TfaMail.
    LoginController Class
    The LoginController class leverages Laravel's built-in trait AuthenticatesUsers to provide login functionality and has the following methods:

    __construct: Constructor method that applies the 'guest' middleware to the class, excluding the 'logout' method, meaning only guests can access these methods.

    showLoginForm: Method to show the login form. It also handles some additional logic if the request comes from specific routes with particular input values, such as if a plan is selected on the Home or Pricing page.

    login: Handles a login request. It includes various checks such as:

    Validating the login request.
    Throttling login attempts if too many failed attempts occur.
    Sending a two-factor authentication (TFA) email if TFA is enabled and credentials are valid.
    Attempting a regular login if TFA is not enabled or not required.
    Handling unsuccessful login attempts.
    validateTfaCode: Validates the TFA code input by the user. It checks if the code is correct and not expired, logs in the user if validation passes, and resets the TFA code.

    resendTfaCode: Resends the TFA code to the user's email address if TFA is enabled.

    resetTfaCode: Private method to reset the user's TFA code in the database.

    Other Properties
    redirectTo: A protected variable that defines where to redirect users after login, set to the '/dashboard' route.
    Two-Factor Authentication (TFA)
    This controller includes robust handling for TFA, integrating the process into the regular login flow. If TFA is enabled and the user's credentials are valid, a TFA code is sent via email. The user must then input this code to complete the login. There are additional functionalities to validate the code, resend it if necessary, and reset it in the database.

    Conclusion
    The LoginController class is a comprehensive implementation for handling user authentication, including standard login procedures and two-factor authentication. It makes use of Laravel's built-in features and expands upon them to create a secure and user-friendly authentication system

*/