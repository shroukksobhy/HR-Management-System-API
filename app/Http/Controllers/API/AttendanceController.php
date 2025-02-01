<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function clockIn(Request $request)
    {
           $request->validate([
                'user_id' => 'required|exists:users,id',
                'date' => 'required|date',
                'clock_in' => 'required|date_format:H:i:s',
                'status' => 'required|string'
            ]);

            $attendance = Attendance::where('user_id', $request->user_id)
                ->where('date', $request->date)
                ->first();
            if ($attendance) {
                return response()->json([
                    'message' => 'You have already clocked in for today'
                ], 400);
            }
    
            $attendance = Attendance::create($request->all());
    
            return response()->json([
                'message' => 'You have successfully clocked in',
                'attendance' => $attendance
            ]);       
    }
    public function clockOut(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'date' => 'required|date',
            'clock_out' => 'required|date_format:H:i:s',
            'status' => 'required|string'
        ]);

        $attendance = Attendance::where('user_id', $request->user_id)
            ->where('date', $request->date)
            ->first();

        if (!$attendance) {
            return response()->json([
                'message' => 'You have not clocked in for today'
            ], 400);
        }

        $attendance->update($request->all());

        return response()->json([
            'message' => 'You have successfully clocked out',
            'attendance' => $attendance
        ]);       
    }
    public function getAttendanceByEmpolyeeId(Request $request){
       
        $attendance = Attendance::where('user_id', $request->employeeId)->get();
        return response()->json([
            'attendance' => $attendance
        ]);
    }

    public function getAllAttendance(){
        $attendance = Attendance::all();
        return response()->json([
            'attendance' => $attendance
        ]);
    }
}
