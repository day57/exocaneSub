<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreTranscriptionRequest;
use App\Http\Requests\API\UpdateTranscriptionRequest;
use App\Http\Resources\TranscriptionResource;
use App\Models\Transcription;
use App\Traits\TranscriptionTrait;
use Illuminate\Http\Request;

class TranscriptionController extends Controller
{
    use TranscriptionTrait;

    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'result']) ? $request->input('search_by') : 'name';
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        return TranscriptionResource::collection(Transcription::where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                if ($searchBy == 'result') {
                    return $query->searchResult($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'favorite' => $favorite, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]))
            ->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreTranscriptionRequest $request
     * @return TranscriptionResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreTranscriptionRequest $request)
    {
        try {
            $created = $this->transcriptionStore($request);

            if ($created) {
                return TranscriptionResource::make($created);
            }
        } catch (\Exception $e) {
            return response()->json([
                'message' => __('An unexpected error has occurred, please try again.') . $e->getMessage(),
                'status' => 500
            ], 500);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Display the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return TranscriptionResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', $request->user()->id]])->first();

        if ($transcription) {
            return TranscriptionResource::make($transcription);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateTranscriptionRequest $request
     * @param $id
     * @return TranscriptionResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateTranscriptionRequest $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($transcription) {
            $updated = $this->transcriptionUpdate($request, $transcription);

            if ($updated) {
                return TranscriptionResource::make($updated);
            }
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($transcription) {
            $transcription->delete();

            return response()->json([
                'id' => $transcription->id,
                'object' => 'transcription',
                'deleted' => true,
                'status' => 200
            ], 200);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }
}


/*

    Namespace and Imports
    These are the same as the previous example, but specific to transcriptions.

    TranscriptionController Class
    It extends Laravel's base Controller class and uses a trait called TranscriptionTrait.

    Methods
    index: Lists all transcriptions.

    Retrieves filters, sorting, and pagination settings from the request.
    Searches and filters the transcriptions based on parameters like search, favorite, search_by, etc.
    Returns a paginated response.
    store: Stores a new transcription.

    Uses a custom request class StoreTranscriptionRequest for input validation.
    Tries to create a new transcription, catching any exceptions that occur.
    Returns the newly created transcription or an error message.
    show: Displays a specific transcription.

    Finds a transcription matching the given id and user_id.
    Returns the transcription or an error message if not found.
    update: Updates a specific transcription.

    Finds a transcription matching the given id and user_id.
    Calls the transcriptionUpdate method to update the transcription.
    Returns the updated transcription or an error message if not found.
    destroy: Deletes a specific transcription.

    Finds a transcription matching the given id and user_id.
    Deletes the transcription if found.
    Returns a success message or an error message if not found.
    Additional Notes
    Request Object: Contains information about the incoming request, such as query parameters and user information.
    Eloquent Query Builder: Used to build complex database queries. Methods like where, when, orderBy, and paginate allow for conditional logic, sorting, and pagination.
    Resources: The TranscriptionResource class formats the transcription data for the response.
    Error Handling: The code includes error handling, with detailed error messages and appropriate HTTP status codes.
    Localization: Uses the __ function to translate error messages, allowing for different languages.
    Traits: The TranscriptionTrait trait is likely where reusable methods like transcriptionStore and transcriptionUpdate are defined, although it's not shown in the code.
    Conclusion
    This class forms the core of a CRUD (Create, Read, Update, Delete) API for managing transcriptions in a Laravel application. It makes extensive use of Laravel's features to provide a flexible, robust interface for interacting with transcriptions, taking advantage of Eloquent for database interactions, request validation for input checking, and resources for response formatting.

*/