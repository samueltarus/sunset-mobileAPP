<?php

namespace App\Http\Controllers\API\Backend\Products;

use App\Http\Controllers\Controller;
use App\Models\ProductTax;
use App\Models\Tax;
use Illuminate\Http\Request;

class TaxesController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:taxes'])->only('index');
        $this->middleware(['permission:add_taxes'])->only(['store']);
        $this->middleware(['permission:edit_taxes'])->only(['edit', 'update']);
        $this->middleware(['permission:publish_taxes'])->only(['updateStatus']);
        $this->middleware(['permission:delete_taxes'])->only(['delete']);
    }

    # tax list
    public function index(Request $request)
    {
        $searchKey = null;
        $is_published = null;

        $taxes = Tax::oldest();
        if ($request->search != null) {
            $taxes = $taxes->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }


        if ($request->is_published != null) {
            $taxes = $taxes->where('is_active', $request->is_published);
            $is_published    = $request->is_published;
        }

        $taxes = $taxes->paginate(paginationNumber());
        return response()->json([
            $taxes,
            $searchKey,
            $is_published
        ],200);
    }

    # tax store
    public function store(Request $request)
    {
        $tax = new Tax;
        $tax->name = $request->name;
        $tax->save();

        flash(localize('Tax has been inserted successfully'))->success();
        return response()->json([
            'message'=>'Tax has been inserted successfully',

        ],200);
    }

    # edit tax
    public function edit($id)
    {
        $tax = Tax::findOrFail($id);
        return response()->json([
            $tax

        ],200);
    }

    # update tax
    public function update(Request $request)
    {
        $tax = Tax::findOrFail($request->id);
        $tax->name = $request->name;

        $tax->save();

        flash(localize('Tax has been updated successfully'))->success();
        return response()->json([
            'message'=>'Tax has been updated successfully'
        ],200);
    }

    # update status 
    public function updateStatus(Request $request)
    {
        $tax = Tax::findOrFail($request->id);
        $tax->is_active = $request->is_active;
        if ($tax->save()) {
            return 1;
        }
        return 0;
    }

    # delete tax
    public function delete($id)
    {
        $tax = Tax::findOrFail($id);
        ProductTax::where('tax_id', $id)->delete();
        $tax->delete();
        flash(localize('Tax has been deleted successfully'))->success();
        return response()->json([
            'message'=>'Tax has been deleted successfully'
        ],200);
    }
}
