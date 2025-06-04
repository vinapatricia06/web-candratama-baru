<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    // Menambahkan properti fillable
    protected $fillable = [
        'progress_projects_id',
        'tanggal_setting',
        'maintenance',
        'status',
        'dokumentasi',
        'biaya_tambahan',
        'kode_transaksi',
        'status_pembayaran'
    ];

    public function project()
    {
        return $this->belongsTo(ProgressProject::class, 'progress_projects_id', 'id');
    }

    public function omset()
    {
        return $this->belongsTo(Omset::class, 'progress_projects_id', 'progress_projects_id');
    }
}
