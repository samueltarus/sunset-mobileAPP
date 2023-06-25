<?php

namespace App\Http\Controllers\API\Backend\Appearance;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Page;
use Illuminate\Http\Request;

class HeaderController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:header'])->only('index');
    }

    # website header configuration
    public function index()
    {
        $categories = Category::where('parent_id', 0)
            ->orderBy('sorting_order_level', 'desc')
            ->with('childrenCategories')
            ->get();

        $pages = Page::latest()->get();
        return response()->json([
            $categories,
            $pages
        ],200);
    }
}
