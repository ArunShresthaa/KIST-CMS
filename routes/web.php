<?php

use App\Http\Controllers\PatientController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PatientController::class, 'index']);
Route::get('/api/patients', [PatientController::class, 'apiPatients']);
