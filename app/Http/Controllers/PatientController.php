<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::with('department')->where('is_active', true)->get();
        return view('welcome', compact('patients'));
    }

    public function apiPatients()
    {
        $patients = Patient::with('department')->where('is_active', true)->get();

        $data = $patients->map(function ($patient) {
            return [
                'bed_no' => $patient->bed_no,
                'hos_no' => $patient->hos_no,
                'patient_name' => $patient->patient_name,
                'age' => $patient->age,
                'sex' => $patient->sex,
                'department' => $patient->department?->name ?? '-',
                'department_color' => $patient->department?->color ?? '#6b7280',
                'admitted_date' => $patient->admitted_date_bs ?? '-',
                'remarks' => $patient->remarks ?? '-',
            ];
        })->values();

        return response()->json([
            'patients' => $data,
            'count' => $patients->count(),
            'updated_at' => now()->format('Y/m/d, h:i:s A'),
        ]);
    }
}
