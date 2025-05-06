<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratWarehouse extends Model
{
    use HasFactory;

    protected $table = 'surat_warehouse';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'jenis_surat',
        'divisi_pembuat',
        'divisi_tujuan',
        'file_path',
        'status_pengajuan',
    ];

    public function getFormattedNomorSuratAttribute()
    {
        $jenis_surat = $this->jenis_surat;
        $id_surat = str_pad($this->id, 3, '0', STR_PAD_LEFT); // Format ID menjadi tiga digit
        $divisi_pembuat = $this->divisi_pembuat;
        $divisi_tujuan = $this->divisi_tujuan;
    
        // Mengambil bulan dan tahun dari created_at
        $bulan = date('n', strtotime($this->created_at)); // Mengambil bulan dalam angka
        $tahun = date('Y', strtotime($this->created_at));
    
        // Konversi bulan ke format romawi
        $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $bulan_romawi = $romawi[$bulan];
    
        // Format nomor surat
        return "{$jenis_surat}/{$id_surat}/{$divisi_pembuat}-{$divisi_tujuan}/{$bulan_romawi}/{$tahun}";
    }
}
