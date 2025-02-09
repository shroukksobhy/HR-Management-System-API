<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Show all the employees if the requester is admin (token)
        $employees = User::where('role', 'employee')->with('profile')->get();
        return response()->json($employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email_company,
                'profile' => $employee->profile,
            ];
        }));
    }
    

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email_company' => 'required|string|email|max:255|unique:users',
            'email_personal' => 'string|email|max:255',
            'password' => 'required|string|min:8',
            "role" => 'required|string',
            "position" => 'required|string',
            "gender" => 'required|string',
            'phone' => 'required|numeric|digits_between:10,15',
            'manager' => 'required|string',
        ]);
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email_company' => $request->email_company,
                'password' => bcrypt($request->password),
                'role' => 'employee',
            ]);
            // Create the profile
            $profile = Profile::create([
              'user_id' => $user->id,
              'address' => $request->address,
              'phone' => $request->phone,
              'position' => $request->position,
              "empID" => Str::random(10),
              "gender" => $request->gender,
              "manager" => "THIS IS HIS MANAGER"
            ]);

            DB::commit();
            return response()->json(['user' => $user, 'profile' => $profile], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'User and profile creation failed', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $employee = User::with('profile')->where('role', 'employee')->findOrFail($id);
            return response()->json($employee);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|required|string|max:255',
        'email_company' => 'sometimes|required|string|email|max:255|unique:users,email,' . $id,
        'email_personal' => 'sometimes|required|string|email|max:255,' . $id,
        'password' => 'sometimes|required|string|min:8',
        'position' => 'sometimes|required|string',
        'gender' => 'sometimes|required|string',
        'phone' => 'sometimes|required|numeric|digits_between:10,15',
        'manager' => 'sometimes|required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();
    try {
        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'email_company', 'password']));
        if ($request->has('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->save();

        $profile = $user->profile;
        $profile->update($request->only(['address', 'phone', 'position', 'gender', 'manager']));
        $profile->save();

        DB::commit();
        return response()->json(['user' => $user, 'profile' => $profile], 200);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'User and profile update failed', 'message' => $e->getMessage()], 500);
    }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $employee = User::findOrFail($id);
            $employee->delete();
            return response()->json(['message' => 'Employee deleted'], 200);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Employee not found'], 404);
        }
    }
}
