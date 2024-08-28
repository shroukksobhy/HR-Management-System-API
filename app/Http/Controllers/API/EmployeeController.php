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
        //SHOW ALL THE employees -> if the requester is admin (token)
        $employees = User::where('role', 'employee')->with('profile')->get();
        return response()->json($employees);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            "role" => 'required|string',
            "position" => 'required|string',
            "gender" => 'required|string',
            'phone' => 'required|numeric|digits_between:10,15',
            'manager' => 'required|string',
        ]);
        // if ($validator->fails()) {
        //     return response()->json($validator->errors(), 422);
        // }
        DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
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
        //        return Employee::findOrFail($id);

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //$employee = Employee::findOrFail($id);
        // $employee->update($request->all());
        // return response()->json($employee, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        // //
        // $employee = Employee::findOrFail($id);
        // $employee->update($request->all());
        // return response()->json($employee, 200);
    }
}
