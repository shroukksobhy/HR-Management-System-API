<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email_company' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            "role" => 'required|string'
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::create([
            'name' => $request->name,
            'email_company' => $request->email_company,
            'password' => Hash::make($request->password),
            "role" => $request->role
        ]);
        try {
            $user->profile()->create([
                'bio' => 'THIS IS BIO',
                'manager' => 'THIS IS HIS MANAGER',
                'position' => 'THIS IS POSITION',
                'empID' => 'THIS IS empID',
                'gender' => 'THIS IS gender',
            ]);
        } catch (\Exception $e) {
            Log::error('Profile creation failed: ' . $e->getMessage());
            // Optionally, you can return a response or throw a custom exception
        }
        //        print($user);
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token, 'token_type' => 'Bearer','user' => $user], 201);
    }
    public function login(Request $request)
    {

        $validator = Validator::make($request->only('email_company', 'password'), [
            'email_company' => 'required|string|email',
            'password' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        $user = User::where('email_company', $request->email_company)->first();
        if (!$user) {
            return response()->json(['email_company' => 'No account found with this email'], 422);
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
