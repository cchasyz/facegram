<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request){
        $input = $request->validate([
            'full_name' => ['required'],
            'bio' => ['required', 'max:100'],
            'username' => ['required', 'min:3', 'unique:users,username', 'regex:/^[a-zA-Z1-9._]+$/'],
            'password' => ['required', 'min:6'],
            'is_private' => ['boolean']
        ]);

        $user = User::create($input);

        $token = $user->createToken('authtoken')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'token' => $token
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function login(Request $request){
        $request->validate([
            'username' => ['required'],
            'password' => ['required']
        ]);

        $user = User::where('username', $request->username)->first();
        if(!$user){
            return response()->json([
                'message' => 'username or password wrong'
            ], 404);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'message' => 'username or password wrong'
            ], 404);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'success',
            'token' => $token
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function logout(){
        auth()->user()->tokens()->delete();

        return response()->json([
            'message' => 'logout success'
        ]);
    }
}
