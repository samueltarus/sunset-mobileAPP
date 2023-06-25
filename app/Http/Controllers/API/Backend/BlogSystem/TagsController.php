<?php

namespace App\Http\Controllers\API\Backend\Backend\BlogSystem;

use App\Http\Controllers\Controller;
use App\Models\BlogTag;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagsController extends Controller
{
    # construct
    public function __construct()
    {
        $this->middleware(['permission:tags'])->only('index');
        $this->middleware(['permission:add_tags'])->only(['store']);
        $this->middleware(['permission:edit_tags'])->only(['edit', 'update']);
        $this->middleware(['permission:delete_tags'])->only(['delete']);
    }

    # tag list
    public function index(Request $request)
    {
        $searchKey = null;
        $tags = Tag::oldest();
        if ($request->search != null) {
            $tags = $tags->where('name', 'like', '%' . $request->search . '%');
            $searchKey = $request->search;
        }

        $tags = $tags->paginate(paginationNumber());
        return response()->json([
            $tags,
            searchKey
        ],200);
    }

    # tag store
    public function store(Request $request)
    {
        $tag = new Tag;
        $tag->name = $request->name;
        $tag->save();

        flash(localize('Tag has been inserted successfully'))->success();
        return response()->json([
            'message'=>'Tag has been inserted successfully'
        ],200);

    }

    # edit tag
    public function edit(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);
        return response()->json([
            $tag
        ],200);
    }

    # update tag
    public function update(Request $request)
    {
        $tag = Tag::findOrFail($request->id);
        $tag->name = $request->name;
        $tag->save();
        flash(localize('Tag has been updated successfully'))->success();
        return response()->json([
            'message'=>'Tag has been updated successfully'
        ],200);
    }


    # delete tag
    public function delete($id)
    {
        $tag = Tag::findOrFail($id);
        BlogTag::where('tag_id', $tag->id)->delete();
        $tag->delete();
        flash(localize('Tag has been deleted successfully'))->success();
        return response()->json([
            'message'=>'Tag has been deleted successfully'
        ],200);
    }
}