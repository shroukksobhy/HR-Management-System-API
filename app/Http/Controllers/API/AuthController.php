<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer','user' => $user], 201);
    }
    public function login(Request $request)
    {

        $validator = Validator::make($request->only('email', 'password'), [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json(['email' => 'No account found with this email'], 422);
            // return redirect()->back()->withErrors(['email' => 'No account found with this email'])->withInput();
        }
        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid login credentials'], 422);
            // return redirect()->back()->withErrors(['password' => 'Invalid login credentials'])->withInput();
        }
        $token = $user->createToken('auth_token')->plainTextToken;
        //sending user if optional i may delete it
        return response()->json(['access_token' => $token,'user' => $user, 'token_type' => 'Bearer']);
    }
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}
