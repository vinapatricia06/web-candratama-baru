<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DebtPaymentProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'progress_projects_id',
        'tanggal_angsuran',
        'nominal',
        'tanggal_pembayaran',
        'kode_transaksi',
        'status_pembayaran',
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
