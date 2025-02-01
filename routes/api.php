<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use Illuminate\Http\Request;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\EmployeeController;
use App\Http\Controllers\API\AttendanceController;
use App\Http\Controllers\API\LeaveRequestController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/user', [UserController::class, 'index']);

});

Route::apiResource('employees', EmployeeController::class);

Route::post('attendance/clock-in', [AttendanceController::class, 'clockIn']);
Route::post('attendance/clock-out', [AttendanceController::class, 'clockOut']);
Route::get('attendance/{employeeId}', [AttendanceController::class, 'getAttendanceByEmpolyeeId']);
Route::get('attendance-all', [AttendanceController::class, 'getAllAttendance']);

Route::post('leave_request', [LeaveRequestController::class, 'store']);