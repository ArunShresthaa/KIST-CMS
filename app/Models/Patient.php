<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'bed_no',
        'hos_no',
        'patient_name',
        'age',
        'sex',
        'department_id',
        'admitted_date',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'admitted_date' => 'date',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }
}
