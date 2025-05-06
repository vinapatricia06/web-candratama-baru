<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratEkspedisi extends Model
{
    use HasFactory;
    protected $table = 'surat_ekspedisi';
    protected $fillable = [
        'nama',
        'divisi',
        'keperluan',
        'file_path',
        'status_pengajuan',
    ];
}
