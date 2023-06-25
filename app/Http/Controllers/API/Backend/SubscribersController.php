<?php

namespace App\Http\Controllers\API\Backend\Stocks;

use Illuminate\Http\Request;
use App\Models\SubscribedUser;
use App\Http\Controllers\Controller;

class SubscribersController extends Controller
{
    # construct
    public function __construct()
    { 
        $this->middleware(['permission:subscribers'])->only(['index']);   
    }
    
    # get subscribers
    public function index(Request $request)
    { 
        $searchKey = null;
        $subscribers = SubscribedUser::latest();
        if ($request->search != null) {
            $subscribers = $subscribers->where('email', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $subscribers = $subscribers->paginate(paginationNumber());
        return response()->json([
           $subscribers,
            $searchKey
        ],200);
    }

    # delete subscribers
    public function delete($id)
    {  
        SubscribedUser::destroy($id);  
        flash(localize('Subscriber has been deleted successfully'))->success();
        return response()->json([
            'message'=>'Subscriber has been deleted successfully'
        ],200);
    }
}
