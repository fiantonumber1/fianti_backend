<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DiagnosisResultController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\VerificationController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);
Route::post('diagnosis-results', [DiagnosisResultController::class, 'store']);
Route::get('diagnosis-results/{id}', [DiagnosisResultController::class, 'show']);
Route::get('/articles', [ArticleController::class, 'index']);
Route::get('/articles/{id}', [ArticleController::class, 'show']);



Route::post('/check-doctor-validity', [OpenAIController::class, 'checkDoctorValidity']);



Route::post('appointments/create', [AppointmentController::class, 'createAppointment']);
Route::get('appointments/user/{user_id}', [AppointmentController::class, 'getUserAppointments']);
Route::get('appointments/doctor/{doctor_id}', [AppointmentController::class, 'getDoctorAppointments']);
Route::delete('appointments/delete/{appointment_id}', [AppointmentController::class, 'deleteAppointment']);
Route::get('doctors', [AppointmentController::class, 'getAllDoctors']);

Route::get('/update-verification', [VerificationController::class, 'updateVerification']);


