<?php

namespace App\Http\Controllers\API\Backend\Logistics;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\State;
use App\Models\City;

class CitiesController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:shipping_cities'])->only('index');
        $this->middleware(['permission:add_shipping_cities'])->only('create');
        $this->middleware(['permission:edit_shipping_cities'])->only('edit');
    }

    # state list
    public function index(Request $request)
    {
        $searchKey = null;
        $searchState = null;
        $cities = City::query();

        if ($request->search != null) {
            $cities = $cities->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        if ($request->searchState) {
            $cities->where('state_id', $request->searchState);
            $searchState = $request->searchState;
        }

        $cities = $cities->orderBy('is_active', 'desc')->paginate(paginationNumber(30));
        return response()->json([
            $cities,
            $searchKey,
            $searchState
        ],200);
    }

    # return view of create form
    public function create()
    {
        $states = State::where('is_active', 1)->get();
        return response()->json([
            $states
        ],200);
    }

    # store new state
    public function store(Request $request)
    {
        $city = new City;
        $city->name        = $request->name;
        $city->state_id    = $request->state_id;
        $city->is_active   = 1;
        $city->save();
        flash(localize('City has been inserted successfully'))->success();
        return response()->json([
            'message'=>'City has been inserted successfully'
        ],200);
    }

    # return view of create form
    public function edit($id)
    {
        $states = State::where('is_active', 1)->get();
        $city = City::findOrFail($id);
        return response()->json([
            $states,
            $city
        ],200);
    }

    # update State  
    public function update(Request $request)
    {
        $city = City::findOrFail((int) $request->id);
        $city->name        = $request->name;
        $city->state_id    = $request->state_id;
        $city->save();
        flash(localize('City has been updated successfully'))->success();
        return response()->json([
            'message'=>'City has been updated successfully'
        ],200);
    }

    # update status 
    public function updateStatus(Request $request)
    {
        $city = City::findOrFail($request->id);
        $city->is_active = $request->is_active;
        $city->save();
        flash(localize('Status updated successfully'))->success();
        return response()->json([
            'message'=>'Status updated successfully'
        ],200);
    }
}
