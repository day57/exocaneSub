<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTranscriptionRequest;
use App\Http\Requests\UpdateTranscriptionRequest;
use App\Models\Transcription;
use App\Traits\TranscriptionTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use League\Csv as CSV;

class TranscriptionController extends Controller
{
    use TranscriptionTrait;

    /**
     * List the Transcriptions.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'result']) ? $request->input('search_by') : 'name';
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $transcriptions = Transcription::where('user_id', $request->user()->id)
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
            ->appends(['search' => $search, 'search_by' => $searchBy, 'favorite' => $favorite, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('transcriptions.container', ['view' => 'list', 'transcriptions' => $transcriptions]);
    }

    /**
     * Show the create Transcription form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('transcriptions.container', ['view' => 'new']);
    }

    /**
     * Show the edit Transcription form.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        return view('transcriptions.container', ['view' => 'edit', 'transcription' => $transcription]);
    }

    /**
     * Show the Transcription.
     */
    public function show(Request $request, $id)
    {
        $transcription = Transcription::where([['id', $id]])->firstOrFail();

        if (!$request->user() || $request->user()->id != $transcription->user_id && $request->user()->role == 0) {
            abort(403);
        }

        return view('transcriptions.container', ['view' => 'show', 'transcription' => $transcription]);
    }

    /**
     * Store the Transcription.
     *
     * @param StoreTranscriptionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(StoreTranscriptionRequest $request)
    {
        try {
            $this->transcriptionStore($request);
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return redirect()->route('transcriptions')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update the Transcription.
     *
     * @param UpdateTranscriptionRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateTranscriptionRequest $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $this->transcriptionUpdate($request, $transcription);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Transcription.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $transcription = Transcription::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $transcription->delete();

        return redirect()->route('transcriptions')->with('success', __(':name has been deleted.', ['name' => $transcription->name]));
    }

    /**
     * Export the Transcriptions.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     * @throws CSV\CannotInsertRecord
     */
    public function export(Request $request)
    {
        if ($request->user()->cannot('dataExport', ['App\Models\User'])) {
            abort(403);
        }

        $now = Carbon::now();
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name', 'result']) ? $request->input('search_by') : 'name';
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';

        $transcriptions = Transcription::where('user_id', $request->user()->id)
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
            ->get();

        $content = CSV\Writer::createFromFileObject(new \SplTempFileObject);

        // Generate the header
        $content->insertOne([__('Type'), __('Transcriptions')]);
        $content->insertOne([__('Date'), $now->tz($request->user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')) . ' ' . $now->tz($request->user()->timezone ?? config('app.timezone'))->format('H:i:s') . ' (' . $now->tz($request->user()->timezone ?? config('app.timezone'))->getOffsetString() . ')']);
        $content->insertOne([__('URL'), $request->fullUrl()]);
        $content->insertOne([__(' ')]);

        // Generate the content
        $content->insertOne([__('ID'), __('Name'), __('Result'), __('Words'), __('Favorite'), __('Updated at'), __('Created at')]);
        foreach ($transcriptions as $transcription) {
            $content->insertOne([$transcription->id, $transcription->name, $transcription->result, $transcription->words, $transcription->favorite, $transcription->updated_at->tz($request->user()->timezone ?? config('app.timezone'))->format(__('Y-m-d')), $transcription->created_at->tz($request->user()->timezone ?? config('app.timezone'))->format(__('Y-m-d'))]);
        }

        return response((string) $content, 200, [
            'Content-Type' => 'text/csv',
            'Content-Transfer-Encoding' => 'binary',
            'Content-Disposition' => 'attachment; filename="' . formatTitle([__('Transcriptions'), config('settings.title')]) . '.csv"',
        ]);
    }
}


/*
    This PHP code snippet defines a TranscriptionController class for a Laravel application. 
    The controller is designed to manage transcriptions—pieces of data that represent text transcribed from audio or video. 

    Methods:
    index: Lists all transcriptions based on various filters like search, favorites, and sorts them accordingly. Returns a view (transcriptions.container) with a list of transcriptions.

    create: Returns a view (transcriptions.container) for creating a new transcription.

    edit: Fetches a specific transcription (based on its ID and the user's ID) to be edited. Returns a view (transcriptions.container) with the transcription's details for editing.

    show: Fetches a specific transcription (based on its ID) to be displayed. If the logged-in user is not the owner of the transcription and doesn't have a role of 0, it aborts with a 403 error. Returns a view (transcriptions.container) to show the transcription.

    store: Uses the transcriptionStore method (likely from TranscriptionTrait) to create a new transcription. Redirects back to the transcriptions list with a success message.

    update: Updates a specific transcription. It fetches the transcription, then uses the transcriptionUpdate method (from TranscriptionTrait) to update it. Redirects back with a success message.

    destroy: Deletes a specific transcription (based on its ID and the user's ID) and redirects back to the transcriptions list with a success message.

    export: Exports all transcriptions of the logged-in user in CSV format. The exported file will have details like ID, Name, Result, Words, Favorite, Updated at, and Created at for each transcription. The header also includes details like the type, date, and URL. The method returns a CSV file as a download.

    Key Features:
    Search and Filtering: The index and export methods support a variety of query parameters to filter the list of transcriptions, such as search, search_by, favorite, sort_by, sort, and per_page.

    Localization: The code utilizes Laravel's localization functions like __() which allows for the application to support multiple languages.

    Authorization: Before some actions, there are checks to ensure that the logged-in user is the owner of the transcription or has the required permissions. For example, the show method checks if the user has the correct role before displaying a transcription. The export method checks if the user has the dataExport permission before allowing export.

    CSV Export: The export method creates a CSV file using the League\Csv library. It contains the details of all transcriptions of the user.

    Security & Best Practices:
    User-owned Data: Most methods ensure that any operations (like editing or deleting) on transcriptions are only allowed if the transcription belongs to the logged-in user.

    Validation: The store and update methods utilize form request validation (e.g., StoreTranscriptionRequest and UpdateTranscriptionRequest) which is a Laravel feature to validate incoming data before it's processed.

    Exception Handling: The store method has a try-catch block to handle any unexpected exceptions that might occur during the creation of a transcription.

    Pagination: In the index method, the transcriptions are fetched with pagination, ensuring that the application remains performant even with a large number of transcriptions.

    In conclusion, this controller manages the CRUD (Create, Read, Update, Delete) operations for transcriptions, with additional functionalities for exporting transcriptions and filtering the list of transcriptions.


*/