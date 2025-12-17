<?php

namespace App\Http\Controllers\Api;

use App;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Appointment;

class AppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(
        $appointments = Appointment::with(['doctor', 'patient'])->get()

    );    
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
      $data = $request->validate([
    'patient_id' => 'required|exists:patients,patient_id',
    'doctor_id'  => 'required|exists:doctors,doctor_id',
    'date'       => 'required|date',
    'status'     => 'nullable|in:pending,confirmed,completed,cancelled',
    'notes'      => 'nullable|string',
]);


        $appointment = Appointment::create($data);
           return response()->json([
            'message' => 'Appointment created successfully',
            'data'    => $appointment
        ], 201);
  
    }

    /**
     * Display the specified resource.
     */
    public function show(Appointment $appointment)
    {
        return response()->json($appointment->load(['patient', 'doctor']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Appointment $appointment)
    {
         $data = $request->validate([
            'patient_id' => 'sometimes|exists:patients,patient_id',
            'doctor_id'  => 'sometimes|exists:doctors,doctor_id',
            'date'       => 'sometimes|date',
            'status'     => 'nullable|in:pending,confirmed,completed,cancelled',
            'notes'      => 'nullable|string',
        ]);

        $appointment->update($data);

        return response()->json([
            'message' => 'Appointment updated successfully',
            'data'    => $appointment
        ]); 
       }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Appointment $appointment)
    {
         $appointment->delete();

        return response()->json([
            'message' => 'Appointment deleted successfully',
            'data'    => $appointment
        ], 204);
    }
}
