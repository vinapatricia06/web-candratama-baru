<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    // Menambahkan properti fillable
    protected $fillable = [
        'nama_klien',
        'no_induk',
        'alamat',
        'project',
        'tanggal_setting',
        'maintenance',
        'status',
        'dokumentasi'
    ];
}
