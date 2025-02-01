<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\LeaveRequest;
class LeaveRequestController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from' => 'required|date',
            'to' => 'required|date',
            'status' => 'required|string',
            'type' => 'required|string'
        ]);

        $leaveRequest = LeaveRequest::create($request->all());

        return response()->json([
            'message' => 'Leave request has been submitted',
            'leave_request' => $leaveRequest
        ]);
    }
    
}
