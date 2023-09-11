<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use App\Traits\MessageTrait;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    use MessageTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $chatId = $request->input('chat_id');
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        return MessageResource::collection(Message::with('chat')
            ->where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when($chatId, function ($query) use ($chatId) {
                return $query->ofChat($chatId);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'search_by' => $searchBy, 'chat_id' => $chatId, 'favorite' => $favorite, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]))
            ->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreMessageRequest $request
     * @return MessageResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreMessageRequest $request)
    {
        try {
            $created = $this->messageStore($request);

            if ($created) {
                return MessageResource::make($created);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('An unexpected error has occurred, please try again.'),
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }
}


/*

    MessageController
    Namespace and Imports:

    namespace App\Http\Controllers\API; declares a namespace to organize code and prevent naming conflicts.
    use statements import classes that the code relies on.
    Class Definition:

    class MessageController extends Controller defines a class named MessageController, inheriting from the Controller base class.
    Traits:

    use MessageTrait; includes the code from the MessageTrait, allowing the class to reuse methods defined in that trait.
    index Method
    Request Parameters:

    The method takes a $request object, which contains information about the HTTP request (e.g., query parameters, body, headers).
    Various filters, sorting, and pagination options are extracted from the request.
    Query Building:

    A query is constructed to retrieve messages from the database. The when method applies conditional constraints (e.g., search by name, filter by chat ID or favorite status).
    Pagination:

    paginate($perPage) splits the results into pages with $perPage items each.
    Response:

    MessageResource::collection transforms the results into a specific JSON structure.
    additional(['status' => 200]) adds an additional status field to the response.
    store Method
    Input Validation:

    StoreMessageRequest $request implies that the request data must meet certain validation rules defined in the StoreMessageRequest class.
    Creating a Message:

    The code calls the messageStore method to create a new message.
    Error Handling:

    try-catch block catches exceptions (errors) and returns a response with an error message and status code 500.
    Response:

    If successful, the method returns a new message using MessageResource::make. If not, a 404 error response is returned.
    General Concepts
    Method Chaining: Several methods are called in sequence (e.g., ->when(...)->orderBy(...)->paginate(...)). This is a common pattern in Laravel's query builder.
    Anonymous Functions: Functions without a name, used as arguments (e.g., function ($query) use ($search) { ... }), allow encapsulating logic inside another function.
    Conditional Logic: in_array checks if a value exists in an array, and the ternary operator (? :) provides a shorthand way to perform conditional assignments.
    Laravel-Specific Concepts
    Resources: MessageResource is likely a Laravel Resource class, used to transform data into a specific JSON structure.
    Response Methods: response()->json(...) constructs a JSON response with specific HTTP status codes.
    Localization: __('Resource not found.') could be a function for translating strings, allowing for easy localization of error messages.
    Overall, the code leverages many features of PHP and Laravel to create a robust and flexible API for managing messages. It combines foundational programming concepts with Laravel-specific patterns to create an organized, maintainable solution.

*/