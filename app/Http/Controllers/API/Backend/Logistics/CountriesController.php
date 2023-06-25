<?php

namespace App\Http\Controllers\API\Backend\Logistics;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountriesController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:shipping_countries'])->only('index');
    }

    # country list
    public function index(Request $request)
    {
        $searchKey = null;
        $countries = Country::query();

        if ($request->search != null) {
            $countries = $countries->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $countries = $countries->orderBy('is_active', 'desc')->paginate(paginationNumber());
        return response()->json([
            $countries,
            $searchKey
        ],200);
    }

    # update status 
    public function updateStatus(Request $request)
    {
        $country = Country::findOrFail($request->id);
        $country->is_active = $request->is_active;
        $country->save();

        foreach ($country->states as $state) {
            $state->is_active = $country->is_active;
            $state->save();

            foreach ($state->cities as $city) {
                $city->is_active = $country->is_active;
                $city->save();
            }
        }

        flash(localize('Status updated successfully'))->success();
        return response()->json([
            'message'=>'Status updated successfully'
        ],200);
    }
}
