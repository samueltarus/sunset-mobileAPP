<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ContactUsMessage;
use Illuminate\Http\Request;

class ContactUsController extends Controller
{
    # store contact us form data
    public function store(Request $request)
    {
        $msg = new ContactUsMessage;
        $msg->name          = $request->name;
        $msg->email         = $request->email;
        $msg->phone         = $request->phone;
        $msg->support_for   = $request->support_for;
        $msg->message       = $request->message;
        $msg->save();
        return response()->json([
            'success' => true,
            'message'=>'Your message has been sent'], 200);
    }

}
