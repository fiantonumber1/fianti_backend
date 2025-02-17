<?php

// app/Http/Controllers/AppointmentController.php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\DoctorDatabase;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Create a new appointment.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|exists:doctor_databases,id',
            'user_id' => 'required|exists:users,id',
            'appointment_time' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        // Check if the appointment time is already taken by the doctor
        $existingAppointment = Appointment::where('doctor_id', $request->doctor_id)
            ->where('appointment_time', $request->appointment_time)
            ->exists();

        if ($existingAppointment) {
            return response()->json(['error' => 'This doctor already has an appointment at this time.'], 400);
        }

        // Create a new appointment
        $appointment = Appointment::create([
            'doctor_id' => $request->doctor_id,
            'user_id' => $request->user_id,
            'appointment_time' => $request->appointment_time,
            'notes' => $request->notes,
        ]);

        return response()->json(['message' => 'Appointment successfully created!', 'appointment' => $appointment], 201);
    }

    /**
     * Get all appointments for a specific user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserAppointments($user_id)
    {
        // Check if the user exists
        $user = User::find($user_id);

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        // Get all appointments for the user
        $appointments = Appointment::where('user_id', $user_id)->with('doctor')->get();

        return response()->json(['appointments' => $appointments]);
    }

    /**
     * Get all available doctors.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllDoctors()
    {
        $doctors = DoctorDatabase::all();

        return response()->json(['doctors' => $doctors]);
    }

    /**
     * Get all appointments for a specific doctor.
     *
     * @param  int  $doctor_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getDoctorAppointments($doctor_id)
    {
        // Check if the doctor exists
        $doctor = DoctorDatabase::find($doctor_id);

        if (!$doctor) {
            return response()->json(['error' => 'Doctor not found.'], 404);
        }

        // Get all appointments for the doctor
        $appointments = Appointment::where('doctor_id', $doctor_id)->with('user')->get();

        return response()->json(['appointments' => $appointments]);
    }

    /**
     * Delete an appointment.
     *
     * @param  int  $appointment_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteAppointment($appointment_id)
    {
        // Find the appointment by ID
        $appointment = Appointment::find($appointment_id);

        if (!$appointment) {
            return response()->json(['error' => 'Appointment not found.'], 404);
        }

        // Delete the appointment
        $appointment->delete();

        return response()->json(['message' => 'Appointment successfully deleted.']);
    }
}

