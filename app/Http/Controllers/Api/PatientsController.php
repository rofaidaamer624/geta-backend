<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Patients;

class PatientsController extends Controller
{
    public function index()
    {
        return response()->json(Patients::all());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,user_id',
            'name'           => 'required|string|max:100',
            'birth_date'     => 'nullable|date',
            'gender'         => 'nullable|in:male,female',
            'diagnosis_date' => 'nullable|date',
            'autism_level'   => 'required|in:mild,moderate,severe',
            'notes'          => 'nullable|string',
        ]);

        $patient = Patients::create($data);

        return response()->json([
            'message' => 'Patient Created Successfully',
            'data' => $patient
        ], 201);
    }

    public function show(Patients $patient)
    {
        return response()->json($patient);
    }

    public function update(Request $request, Patients $patient)
    {
        $data = $request->validate([
            'user_id'        => 'required|exists:users,user_id',
            'name'           => 'required|string|max:100',
            'birth_date'     => 'nullable|date',
            'gender'         => 'nullable|in:male,female',
            'diagnosis_date' => 'nullable|date',
            'autism_level'   => 'required|in:mild,moderate,severe',
            'notes'          => 'nullable|string',
        ]);

        $patient->update($data);

        return response()->json([
            'message'=> 'Patient updated successfully',
            'data'=> $patient
        ]);
    }

    public function destroy(Patients $patient)
    {
        $patient->delete();
        return response()->json(null, 204);
    }
}
