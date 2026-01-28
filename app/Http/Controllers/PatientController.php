<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function index()
    {
        $patients = Patient::where('is_active', true)->get();
        return view('welcome', compact('patients'));
    }
}
