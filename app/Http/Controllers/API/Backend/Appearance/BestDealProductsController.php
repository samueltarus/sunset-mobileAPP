<?php

namespace App\Http\Controllers\API\Backend\Appearance;

use App\Http\Controllers\Controller;
use App\Models\Product;

class BestDealProductsController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:homepage'])->only('index');
    }

    # best deal products
    public function index()
    {
        $products = Product::isPublished()->get();
        return response()->json([
            $products
        ],200);
    }
}
