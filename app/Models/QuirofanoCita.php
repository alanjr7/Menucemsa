<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuirofanoCita extends Model
{
    use HasFactory;

    protected $table = 'quirofano_citas';

    protected $fillable = [
        'patient_name',
        'procedure_name',
        'surgeon_name',
        'scheduled_at',
        'operating_room',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
    ];
}
