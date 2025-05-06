<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratCleaning extends Model
{
    use HasFactory;
    // Specify the table name (optional if it's following Laravel's naming convention)
    protected $table = 'surat_cleanings';

    // Define the attributes that can be mass-assigned
    protected $fillable = [
        'nama',
        'divisi',
        'keperluan',
        'file_path',
        'status_pengajuan',
    ];
}
