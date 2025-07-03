<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgressProject extends Model
{
    use HasFactory;

    protected $fillable = [
        'teknisi_id',
        'klien_id',
        'project',
        'tanggal_mulai',
        'tanggal_selesai',
        'dokumentasi',
        'nominal',
        'uang_muka',
        'is_hutang',
        'kode_transaksi',
        'snap_token',
        'status_pembayaran',
        'status',
    ];

    public function teknisi()
    {
        return $this->belongsTo(User1::class, 'teknisi_id', 'id_user');
    }

    public function klien()
    {
        return $this->belongsTo(Klien::class, 'klien_id', 'id');
    }

    public function omset()
    {
        return $this->belongsTo(Omset::class, 'id', 'progress_projects_id');
    }
    public function omsets()
    {
        return $this->HasMany(Omset::class, 'progress_projects_id', 'id');
    }
    public function debtPayments()
    {
        return $this->hasMany(DebtPaymentProject::class, 'progress_projects_id', 'id');
    }
}
