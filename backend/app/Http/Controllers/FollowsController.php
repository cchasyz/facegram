<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Follows;
use Illuminate\Http\Request;

class FollowsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function getFollowing($username){
        $user = User::where('username', $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        $following = Follows::where('follower_id', $user->id)->get();
        $output = $following->map(function($f){
            $user = User::where('id', $f->following_id)->first();
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'bio' => $user->bio,
                'is_private' => $user->is_private,
                'created_at' => $user->created_at,
                'is_requested' => $f->is_accepted ? 'false' : 'true'
            ];
        });
        return response()->json([
            'following' => $output
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function follow($username){
        $me = auth()->user();
        $user = User::where('username', $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        if($me->id === $user->id){
            return response()->json([
                'message' => 'you are not allowed to follow urself!'
            ],422);
        }
        $follow = Follows::where('follower_id', $me->id)->where('following_id', $user->id)->first() ?? null;
        if($follow !== null){
            return response()->json([
                'message' => 'you are already following',
                'status' => $follow->is_accepted ? 'following' : 'requested'
            ], 422);
        }
        if($user->is_private){
            $newfollow = Follows::create([
                'follower_id' => $me->id,
                'following_id' => $user->id,
                'is_accepted' => 0
            ]);
        } else {
            $newfollow = Follows::create([
                'follower_id' => $me->id,
                'following_id' => $user->id,
                'is_accepted' => 1
            ]);
        }
        return response()->json([
            'message' => 'success',
            'status' => $newfollow->is_accepted ? 'following' : 'requested'
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function unfollow($username){
        $me = auth()->user();
        $user = User::where('username', $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        $follow = Follows::where('follower_id', $me->id)->where('following_id', $user->id)->first() ?? null;
        if($follow === null){
            return response()->json([
                'message' => 'you are not following this user',
            ], 422);
        }
        $follow->delete();
    }

    /**
     * Display the specified resource.
     */
    public function accept($username){
        $me = auth()->user();
        $user = User::where('username', $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        $follow = Follows::where('follower_id', $user->id)->where('following_id', $me->id)->first() ?? null;
        if($follow === null){
            return response()->json([
                'message' => 'user is not following you',
            ], 422);
        }
        if($follow->is_accepted){
            return response()->json([
                'message' => 'follow request already accepted',
            ], 422);
        }
        $follow->update([
            'is_accepted' => 1
        ]);
        return response()->json([
            'message' => 'success'
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function getFollowers($username){
        $user = User::where('username', $username)->first();
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        $follower = Follows::where('following_id', $user->id)->get();
        $output = $follower->map(function($f){
            $user = User::where('id', $f->follower_id)->first();
            return [
                'id' => $user->id,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'bio' => $user->bio,
                'is_private' => $user->is_private,
                'created_at' => $user->created_at,
                'is_requested' => $f->is_accepted ? 'false' : 'true'
            ];
        });
        return response()->json([
            'followers' => $output
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function getUsers(){
        $me = auth()->user();

        $followers = Follows::where('following_id', $me->id)->pluck('follower_id');
        $nonFollowingUsers = User::whereNotIn('id', $followers)->get();

        return response()->json([
            'message' => 'success',
            'users' => $nonFollowingUsers
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function getDetailUsers($username){
        $user = User::where('username', $username)->with('posts.attachments')->first();
        if(!$user){
            return response()->json([
                'message' => 'user not found'
            ], 404);
        }
        $me = auth()->user();
        $following = Follows::where('follower_id', $user->id)->get();
        $followers = Follows::where('following_id', $user->id)->get();
        $postCount = $user->posts->count();
        $check = Follows::where('follower_id', $me->id)->where('following_id', $user->id)->first();
            $output = [
                'id' => $user->id,
                    'full_name' => $user->full_name,
                    'username' => $user->username,
                    'bio' => $user->bio,
                    'is_private' => $user->is_private,
                    'created_at' => $user->created_at,
                    'is_your_accout' => $me->id === $user->id ? 'true' : 'false',
                    'following_status' => $check ? ($check->is_accepted ? 'following' : 'requested') : 'not-following',
                    'posts_count' => $postCount,
                    'followers_count' => $followers->count(),
                    'following_count' => $following->count(),
                    'posts' => $user->is_private ? ($check ? ($check->is_accepted ? $user->posts : []) : []) : $user->posts,
            ];
        return response()->json([
            'message' => 'success',
            'data' => $output
        ]);
    }
}
