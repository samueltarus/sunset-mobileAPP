<?php

namespace App\Http\Controllers\API;
use Illuminate\Http\Request;
use App\Models\SubscribedUser;
use App\Http\Controllers\Controller;

class SubscribersController extends Controller
{
    # store new subscribers
    public function store(Request $request)
    {
        $subscriber = SubscribedUser::where('email', $request->email)->first();
        if($subscriber == null){
            $subscriber = new SubscribedUser;
            $subscriber->email = $request->email;
            $subscriber->save();
            flash(localize('You have subscribed successfully'))->success();
            return response()->json([
                'success' => true,
                'message' => 'You have subscribed successfully'], 200);
        }
        else{
            flash(localize('You are  already a subscriber'))->error();
            return response()->json([
                'success' => true,
                'message' => 'You are  already a subscriber'], 200);
        }

    }
}
