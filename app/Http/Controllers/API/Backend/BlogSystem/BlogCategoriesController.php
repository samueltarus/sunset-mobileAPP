<?php
namespace App\Http\Controllers\API\Backend\Backend\BlogSystem;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoriesController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:blog_categories'])->only('index');
        $this->middleware(['permission:add_blog_categories'])->only(['store']);
        $this->middleware(['permission:edit_blog_categories'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_blog_categories'])->only(['delete']);
    }

    # unit list
    public function index(Request $request)
    {
        $searchKey = null;
        $categories = BlogCategory::oldest();
        if ($request->search != null) {
            $categories = $categories->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $categories = $categories->paginate(paginationNumber());
        return response()->json([
            $categories,
            $searchKey
        ],200);
    }

    # unit store
    public function store(Request $request)
    {
        $category = new BlogCategory;
        $category->name = $request->name;
        $category->save();

        flash(localize('Category has been inserted successfully'))->success();
        return response()->json([
            'message'=>'Category has been inserted successfully',
        ],200);
    }

    # edit unit
    public function edit(Request $request, $id)
    {
        $category = BlogCategory::findOrFail($id);
        return response()->json([
            $category
        ],200);
    }

    # update unit
    public function update(Request $request)
    {
        $category = BlogCategory::findOrFail($request->id);
        $category->name = $request->name;
        $category->save();
        flash(localize('Category has been updated successfully'))->success();
        return response()->json([
           'message'>'Category has been updated successfully'
        ],200);
    }


    # delete unit
    public function delete($id)
    {
        $category = BlogCategory::findOrFail($id);
        Blog::where('blog_category_id', $category->id)->delete();
        $category->delete();
        flash(localize('Category has been deleted successfully'))->success();
        return response()->json([
            'message'>'Category has been deleted successfully'
        ],200);
    }
}
