<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\StoreDocumentRequest;
use App\Http\Requests\API\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Traits\DocumentTrait;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use DocumentTrait;

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
        $templateId = $request->input('template_id');
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        return DocumentResource::collection(Document::with('template')
            ->where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                if ($searchBy == 'result') {
                    return $query->searchResult($search);
                }
                return $query->searchName($search);
            })
            ->when(isset($templateId), function ($query) use ($templateId) {
                return $query->ofTemplate($templateId);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'template_id' => $templateId, 'favorite' => $favorite, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]))
            ->additional(['status' => 200]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreDocumentRequest $request
     * @return DocumentResource|\Illuminate\Http\JsonResponse
     */
    public function store(StoreDocumentRequest $request)
    {
        if (!$request->input('variations')) {
            try {
                $created = $this->documentStore($request, $request->input('prompt'));

                if ($created) {
                    return DocumentResource::make($created);
                }
            } catch (\Exception $e) {
                return response()->json([
                    'message' => __('An unexpected error has occurred, please try again.') . $e->getMessage(),
                    'status' => 500
                ], 500);
            }
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
     * @return DocumentResource|\Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $document = Document::where([['id', '=', $id], ['user_id', $request->user()->id]])->first();

        if ($document) {
            return DocumentResource::make($document);
        }

        return response()->json([
            'message' => __('Resource not found.'),
            'status' => 404
        ], 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateDocumentRequest $request
     * @param $id
     * @return DocumentResource|\Illuminate\Http\JsonResponse
     */
    public function update(UpdateDocumentRequest $request, $id)
    {
        $document = Document::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($document) {
            $updated = $this->documentUpdate($request, $document);

            if ($updated) {
                return DocumentResource::make($updated);
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
        $document = Document::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->first();

        if ($document) {
            $document->delete();

            return response()->json([
                'id' => $document->id,
                'object' => 'document',
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

    The DocumentController class in this PHP code snippet is another controller class for a Laravel application. This class handles CRUD (Create, Read, Update, Delete) operations for document resources. Here's a summary of each method:

    index(Request $request): This method retrieves a paginated list of document resources for the authenticated user, with optional filtering by search text, template ID, favorites, sorting options, and pagination. The result is wrapped in the DocumentResource class and returned as a JSON response.

    store(StoreDocumentRequest $request): This method stores a new document resource. It uses a custom request class (StoreDocumentRequest) to validate the input and a method documentStore from the DocumentTrait to handle the creation. If the creation is successful, it returns the created document, otherwise, it returns an error response.

    show(Request $request, $id): This method retrieves a specific document resource by ID, making sure it belongs to the authenticated user. If found, it returns the document wrapped in the DocumentResource class, otherwise, it returns a 404 error response.

    update(UpdateDocumentRequest $request, $id): This method updates an existing document resource identified by ID. It uses a custom request class (UpdateDocumentRequest) to validate the input and a method documentUpdate from the DocumentTrait to handle the update. If the update is successful, it returns the updated document, otherwise, it returns a 404 error response.

    destroy(Request $request, $id): This method deletes a specific document resource by ID, ensuring it belongs to the authenticated user. If successfully deleted, it returns a success response; otherwise, it returns a 404 error response.

    use DocumentTrait;: This line indicates that the controller is utilizing a trait called DocumentTrait. The trait likely includes common functionality used by the controller methods, such as the documentStore and documentUpdate methods.

    This code snippet is well-written and follows Laravel's conventions for resourceful controllers. By using custom request classes and traits, it keeps the code organized and reusable. The search, filter, and sorting functionality in the index method is particularly flexible and would be useful for implementing complex listing views in an API. It also uses Eloquent's with method to eager load the related template data, which can improve performance by reducing the number of queries.

    However, in the store method, there is a condition checking for the absence of variations in the request input but no logic is provided for handling the case where variations are present. The current code will return a 404 error if variations are included in the request. Depending on the intended behavior, additional logic may need to be added to handle this scenario.

*/