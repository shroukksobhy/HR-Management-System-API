<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;

class UserController extends Controller
{
    public function index(Request $request)
    {
        // Retrieve and return a list of users and their profiles
        $users = User::with('profile')->get();
        return response()->json($users);
    }
}
