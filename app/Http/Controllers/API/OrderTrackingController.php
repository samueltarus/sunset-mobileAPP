<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderGroup;
use Illuminate\Http\Request;

class OrderTrackingController extends Controller
{
    # track orders
    public function index(Request $request)
    {

        if ($request->code != null) {
            $searchCode = $request->code;
            $orderGroup = OrderGroup::where('order_code', $searchCode)->first();
            $order = null;

            if (!is_null($orderGroup)) {
                $order = Order::where('user_id', auth()->user()->id)->where('order_group_id', $orderGroup->id)->first();
            }

            if (!is_null($order)) {
                return response()->json([
                    'success' => true,
                    'order' => $order, 'searchCode' => $searchCode], 200);
            } else {
                flash(localize('No order found by this code'))->error();
                return response()->json([
                    'success' => true,
                    'searchCode' => $searchCode], 200);
            }
        } else {
            return response()->json([
                ], 200);
        }


    }
}
