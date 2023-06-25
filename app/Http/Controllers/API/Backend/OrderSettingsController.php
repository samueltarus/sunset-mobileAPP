<?php

namespace App\Http\Controllers\API\Backend\Stocks;

use App\Http\Controllers\Controller;
use App\Models\ScheduledDeliveryTimeList;
use Illuminate\Http\Request;

class OrderSettingsController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:order_settings'])->only(['index', 'store', 'edit', 'update', 'delete']);
    }

    # order settings view
    public function index()
    {
        $slots = ScheduledDeliveryTimeList::orderBy('sorting_order', 'ASC')->get();
        return response()->json([
            $slots
        ],200);
    }

    # store time slot
    public function store(Request $request)
    {
        $timeSlot = new ScheduledDeliveryTimeList;
        $timeSlot->timeline         = $request->timeline;
        $timeSlot->sorting_order    = $request->sorting_order;
        $timeSlot->save();
        flash(localize('Slot has been saved successfully'))->success();
        return response()->json([
            'message'=>'Slot has been saved successfully'
        ],200);
    }

    # edit form
    public function edit($id)
    {
        $slot = ScheduledDeliveryTimeList::find($id);
        return response()->json([
            $slot
        ],200);
    }

    # update timeslot
    public function update(Request $request)
    {
        $timeSlot = ScheduledDeliveryTimeList::where('id', $request->id)->first();
        $timeSlot->timeline         = $request->timeline;
        $timeSlot->sorting_order    = $request->sorting_order;
        $timeSlot->save();

        flash(localize('Slot has been updated successfully'))->success();
        return response()->json([
            'message'=>'Slot has been updated successfully'
        ],200);
    }

    # delete timeslot
    public function delete($id)
    {
        ScheduledDeliveryTimeList::where('id', $id)->delete();
        flash(localize('Slot has been deleted successfully'))->success();
        return response()->json([
            'message'=>'Slot has been deleted successfully'
        ],200);
    }
}
