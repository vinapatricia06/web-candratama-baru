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
        'tanggal_setting',
        'dokumentasi',
        'nominal',
        'status_pembayaran',
        'status',
        'serah_terima' // Tambahkan serah_terima di sini
    ];

    public function teknisi()
    {
        return $this->belongsTo(User1::class, 'teknisi_id', 'id_user');
    }

    public function klien()
    {
        return $this->belongsTo(Klien::class, 'klien_id', 'id');
    }
}
