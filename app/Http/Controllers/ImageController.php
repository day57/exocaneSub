<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Http\Requests\UpdateTemplateRequest;
use App\Models\Image;
use App\Models\Template;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    use ImageTrait;

    /**
     * List the Images.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $searchBy = in_array($request->input('search_by'), ['name']) ? $request->input('search_by') : 'name';
        $resolution = $request->input('resolution');
        $style = $request->input('style');
        $medium = $request->input('medium');
        $filter = $request->input('filter');
        $favorite = $request->input('favorite');
        $sortBy = in_array($request->input('sort_by'), ['id', 'name']) ? $request->input('sort_by') : 'id';
        $sort = in_array($request->input('sort'), ['asc', 'desc']) ? $request->input('sort') : 'desc';
        $perPage = in_array($request->input('per_page'), [10, 25, 50, 100]) ? $request->input('per_page') : config('settings.paginate');

        $images = Image::where('user_id', $request->user()->id)
            ->when($search, function ($query) use ($search, $searchBy) {
                return $query->searchName($search);
            })
            ->when($resolution, function ($query) use ($resolution) {
                return $query->ofResolution($resolution);
            })
            ->when($style, function ($query) use ($style) {
                return $query->ofStyle($style);
            })
            ->when($medium, function ($query) use ($medium) {
                return $query->ofMedium($medium);
            })
            ->when($filter, function ($query) use ($filter) {
                return $query->ofFilter($filter);
            })
            ->when(isset($favorite) && is_numeric($favorite), function ($query) use ($favorite) {
                return $query->ofFavorite($favorite);
            })
            ->orderBy($sortBy, $sort)
            ->paginate($perPage)
            ->appends(['search' => $search, 'style' => $style, 'medium' => $medium, 'filter' => $filter, 'resolution' => $resolution, 'favorite' => $favorite, 'search_by' => $searchBy, 'sort_by' => $sortBy, 'sort' => $sort, 'per_page' => $perPage]);

        return view('images.container', ['view' => 'list', 'images' => $images]);
    }

    /**
     * Show the create Image form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function create()
    {
        return view('images.container', ['view' => 'new']);
    }

    /**
     * Show the edit Image form.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function edit(Request $request, $id)
    {
        $image = Image::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        return view('images.container', ['view' => 'edit', 'image' => $image]);
    }

    /**
     * Show the Image.
     */
    public function show(Request $request, $id)
    {
        $image = Image::where([['id', $id]])->firstOrFail();

        if (!$request->user() || $request->user()->id != $image->user_id && $request->user()->role == 0) {
            abort(403);
        }

        return view('images.container', ['view' => 'show', 'image' => $image]);
    }

    /**
     * Store the Image.
     *
     * @param StoreImageRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function store(StoreImageRequest $request)
    {
        try {
            $images = $this->imagesStore($request);
        } catch (\Exception $e) {
            return back()->with('error', __('An unexpected error has occurred, please try again.') . $e->getMessage())->withInput();
        }

        return view('images.container', ['view' => 'new', 'images' => $images, 'name' => $request->input('name'), 'description' => $request->input('description'), 'style' => $request->input('style'), 'medium' => $request->input('medium'), 'filter' => $request->input('filter'), 'resolution' => $request->input('resolution'), 'variations' => $request->input('variations')]);
    }

    /**
     * Update the Image.
     *
     * @param UpdateImageRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateImageRequest $request, $id)
    {
        $image = Image::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $this->imageUpdate($request, $image);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Process the Template.
     *
     * @param UpdateTemplateRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function processShow(UpdateTemplateRequest $request, $id)
    {
        $template = Template::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $this->templateUpdate($request, $template);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the Image.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy(Request $request, $id)
    {
        $image = Image::where([['id', '=', $id], ['user_id', '=', $request->user()->id]])->firstOrFail();

        $image->delete();

        return redirect()->route('images')->with('success', __(':name has been deleted.', ['name' => $image->name]));
    }
}


/*

    The ImageController class is responsible for handling various CRUD operations (Create, Read, Update, Delete) related to images in a Laravel application. It also utilizes an ImageTrait for some functionalities. Below is an overview of the key methods in this controller:

    index(Request $request)
    Lists all the images for the logged-in user. It supports multiple filters and search options:

    Search: Allows searching for images by name.
    Resolution, Style, Medium, Filter, Favorite: Allows filtering images by resolution, style, medium, filter, or favorite status.
    Sorting and Pagination: Supports ordering by ID or name in ascending or descending order and allows specifying the number of items per page.
    create()
    Shows the form to create a new image.

    edit(Request $request, $id)
    Displays the edit form for a specific image by its ID and ensures that the logged-in user is the owner of the image.

    show(Request $request, $id)
    Displays a specific image by its ID. It ensures that the user has the right permission to view the image, aborting with a 403 error if not.

    store(StoreImageRequest $request)
    Handles the storing of a new image. The method accepts a validated request and calls a method from the ImageTrait to perform the actual storing. In case of an error, it redirects back with an error message.

    update(UpdateImageRequest $request, $id)
    Updates an existing image identified by its ID. It checks that the logged-in user is the owner of the image and calls a method from the ImageTrait to perform the actual update. Upon success, it redirects back with a success message.

    processShow(UpdateTemplateRequest $request, $id)
    Processes a specific template by its ID, updating its attributes. It ensures that the logged-in user is the owner of the template and calls a method to perform the actual update. Upon success, it redirects back with a success message.

    destroy(Request $request, $id)
    Deletes a specific image by its ID, ensuring that the logged-in user is the owner of the image. It performs the deletion and redirects to the images route with a success message.

    Summary
    The ImageController class encapsulates the logic required to handle image management in the application. It provides features to list, create, edit, view, store, update, process, and delete images, offering a clean and structured way to manage images. It also makes use of Laravel's conditional query building and validation to create a robust and flexible controller.

*/