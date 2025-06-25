<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Posts;
use App\Models\Follows;
use Illuminate\Http\Request;
use App\Models\PostAttachments;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request){
        $request->validate([
            'page' => ['sometimes', 'integer', 'min:0'],
            'size' => ['sometimes', 'integer', 'min:1']
        ]);

        $page = 0;
        $size = 10;

        if($request->has('page')){
            $page = $request->page;
        }
        if($request->has('size')){
            $size = $request->size;
        }

        $skip = $page * $size;

        $query = Posts::with('user', 'attachments')->orderBy('created_at', 'desc');
        $posts = $query->skip($skip)->take($size)->get();

        return response()->json([
            'message' => 'success',
            'page' => $page,
            'size' => $size,
            'data' => $posts
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request){
        $request->validate([
            'caption' => ['required'],
            'attachments' => ['required', 'array'],
            'attachments.*' => ['required', 'image', 'mimes:jpg,jpeg,webp,png,gif']
        ]);

        $user = auth()->user();

        $post = Posts::create([
            'user_id' => $user->id,
            'caption' => $request->caption
        ]);

        foreach ($request->attachments as $att) {
            $path = $att->store('posts', 'public');

            PostAttachments::create([
                'storage_path' => $path,
                'post_id' => $post->id
            ]);
        }

        return response()->json([
            'message' => 'create post success',
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function getUserRequest(){
        $me = auth()->user();
        $rawr = Follows::where('following_id', $me->id)->where('is_accepted', false)->pluck('follower_id');
        $users = User::whereIn('id', $rawr)->get();

        return response()->json([
            'message' => 'success',
            'data' => $users
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($postId){
        $user = auth()->user();
        $post = Posts::where('id', $postId)->first();
        if(!$post){
            return response()->json([
                'message' => 'post not found'
            ], 404);
        }
        if($post->user_id !== $user->id){
            return response()->json([
                'message' => 'forbidden access'
            ], 403);
        }
        $post->delete();
        return response()->json([
            'message' => 'success'
        ]);
    }
}
