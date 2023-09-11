<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AccountResource;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    /**
     * Display the resource.
     *
     * @param Request $request
     * @return AccountResource|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->user()) {
            return AccountResource::make($request->user());
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }
}

/*

    The given code snippet is a Laravel controller class named AccountController, which extends the base Controller class. This class is designed to handle account-related API requests and contains a single method:

    index(Request $request): This method is responsible for displaying the authenticated user's account information.

    If the request contains an authenticated user (obtained through $request->user()), it returns the user's data using the AccountResource class. This class is likely a custom API resource class that would transform the user data into the desired JSON structure.

    If there is no authenticated user, the method returns a 404 JSON response with a 'Resource not found.' message and a status code of 404.

    Overall, this controller is straightforward and is designed to be used within an API context, specifically to retrieve information about the authenticated user's account. It assumes that there is middleware in place (outside of this code snippet) that handles authentication and ensures that only authenticated users can access this endpoint.

*/