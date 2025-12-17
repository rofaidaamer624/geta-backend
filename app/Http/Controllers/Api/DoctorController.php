<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    /**
     * Display a listing of the resource.
     * GET /api/doctors
     */
    public function index()
    {
        // لو حابة لاحقًا تعملي pagination ممكن نعدّل هنا
        return response()->json(Doctor::all());
    }

    /**
     * Store a newly created resource in storage.
     * POST /api/doctors
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:100',
            'speciality'       => 'required|string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'bio'              => 'nullable|string',
            'clinic_location'  => 'nullable|string|max:255',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        $doctor = Doctor::create($data);

        return response()->json([
            'message' => 'Doctor created successfully',
            'data'    => $doctor,
        ], 201);
    }

    /**
     * Display the specified resource.
     * GET /api/doctors/{doctor}
     */
    public function show(Doctor $doctor)
    {
        // مفيش بقى user علشان شيلنا العلاقة
        return response()->json($doctor);
    }

    /**
     * Update the specified resource in storage.
     * PUT/PATCH /api/doctors/{doctor}
     */
    public function update(Request $request, Doctor $doctor)
    {
        $data = $request->validate([
            'name'             => 'sometimes|string|max:100',
            'speciality'       => 'sometimes|string|max:100',
            'experience_years' => 'nullable|integer|min:0',
            'bio'              => 'nullable|string',
            'clinic_location'  => 'nullable|string|max:255',
            'consultation_fee' => 'nullable|numeric|min:0',
        ]);

        $doctor->update($data);

        return response()->json([
            'message' => 'Doctor updated successfully',
            'data'    => $doctor,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * DELETE /api/doctors/{doctor}
     */
    public function destroy(Doctor $doctor)
    {
        $doctor->delete();

        return response()->json([
            'message' => 'Doctor deleted successfully',
        ], 200);
    }
}
