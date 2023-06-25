<?php

namespace App\Http\Controllers\API\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\Language;
use App\Models\Variation;
use App\Models\VariationLocalization;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VariationsController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:variations'])->only('index');
        $this->middleware(['permission:add_variations'])->only(['store']);
        $this->middleware(['permission:edit_variations'])->only(['edit', 'update']);
        $this->middleware(['permission:publish_variations'])->only(['updateStatus']);
    }

    # variation list
    public function index(Request $request)
    {
        $searchKey = null;
        $is_published = null;

        $variations = Variation::oldest();
        if ($request->search != null) {
            $variations = $variations->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->is_published != null) {
            $variations = $variations->where('is_active', $request->is_published);
            $is_published    = $request->is_published;
        }


        $variations = $variations->paginate(paginationNumber());
        return response()->json([
            $variations,
            $searchKey,
            $is_published
        ],200);

    }

    # variation store
    public function store(Request $request)
    {
        $variation = new Variation;
        $variation->name = $request->name;

        $variation->save();

        $variationLocalization = VariationLocalization::firstOrNew(['lang_key' => env('DEFAULT_LANGUAGE'), 'variation_id' => $variation->id]);
        $variationLocalization->name = $variation->name;

        $variationLocalization->save();

        flash(localize('Variation has been inserted successfully'))->success();
        return response()->json([
            'message'=>'Variation has been inserted successfully',
        ],200);
    }

    # edit variation
    public function edit(Request $request, $id)
    {
        $lang_key = $request->lang_key;
        $language = Language::where('is_active', 1)->where('code', $lang_key)->first();
        if (!$language) {
            flash(localize('Language you are trying to translate is not available or not active'))->error();
            return response()->json([
                'message'=>'Language you are trying to translate is not available or not active',
            ],200);
        }
        $variation = Variation::findOrFail($id);
        return response()->json([
           $variation,
            $lang_key
        ],200);
    }

    # update variation
    public function update(Request $request)
    {
        $variation = Variation::findOrFail($request->id);

        if ($request->lang_key == env("DEFAULT_LANGUAGE")) {
            $variation->name = $request->name;
        }

        $variationLocalization = VariationLocalization::firstOrNew(['lang_key' => $request->lang_key, 'variation_id' => $variation->id]);
        $variationLocalization->name = $request->name;

        $variation->save();
        $variationLocalization->save();

        flash(localize('Variation has been updated successfully'))->success();
        return response()->json([
           'message'=>'Variation has been updated successfully',
        ],200);
    }

    # update status 
    public function updateStatus(Request $request)
    {
        $variation = Variation::findOrFail($request->id);
        $variation->is_active = $request->is_active;
        if ($variation->save()) {
            return 1;
        }
        return 0;
    }

    # delete variation
    public function delete($id)
    {
        $variation = Variation::findOrFail($id);
        $variation->delete();
        flash(localize('Variation has been deleted successfully'))->success();
        return response()->json([
            'message'=>'Variation has been deleted successfully',
        ],200);
    }
}
