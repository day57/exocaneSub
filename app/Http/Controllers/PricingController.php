<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Template;

class PricingController extends Controller
{
    /**
     * Show the Pricing page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $plans = Plan::where('visibility', 1)->orderBy('position')->orderBy('id')->get();

        $templates = Template::global()->premade()->orderBy('name', 'asc')->get();

        $customTemplates = Template::global()->custom()->orderBy('name', 'asc')->get();

        return view('pricing.index', ['plans' => $plans, 'templates' => $templates, 'customTemplates' => $customTemplates]);
    }
}


/*
    This PHP code snippet defines a `PricingController` class in a Laravel application. The controller contains one method, `index`, which is responsible for displaying the pricing page. Here's what the code does:

    ### Namespace and Imports

    ```php
    namespace App\Http\Controllers;

    use App\Models\Plan;
    use App\Models\Template;
    ```

    - The code resides in the `App\Http\Controllers` namespace.
    - It imports two model classes, `Plan` and `Template`, that represent different aspects related to pricing and templates in the application's database.

    ### The `index` Method

    ```php
    public function index()
    {
        $plans = Plan::where('visibility', 1)->orderBy('position')->orderBy('id')->get();
        $templates = Template::global()->premade()->orderBy('name', 'asc')->get();
        $customTemplates = Template::global()->custom()->orderBy('name', 'asc')->get();
        return view('pricing.index', ['plans' => $plans, 'templates' => $templates, 'customTemplates' => $customTemplates]);
    }
    ```

    - The method retrieves three different sets of data:
    1. **Plans**: It fetches all the `Plan` objects where the `visibility` field is set to `1`, ordering them by the `position` field and then by the `id` field.
    2. **Templates**: It retrieves all the global, premade templates, ordering them by the `name` field in ascending order. The `global` and `premade` methods are assumed to be custom query scope methods defined on the `Template` model.
    3. **Custom Templates**: Similar to templates, but for custom templates, presumably defined by the `global` and `custom` query scope methods on the `Template` model.
    - Finally, it returns a view named `pricing.index`, passing the retrieved plans, templates, and custom templates to the view.

    ### Summary

    The `PricingController` class is responsible for handling the logic needed to display the pricing page of the application. It gathers information about available plans and templates (both premade and custom) and passes this data to a Blade template for rendering. The Blade template corresponding to `pricing.index` would be responsible for displaying this information in the appropriate HTML format.

*/