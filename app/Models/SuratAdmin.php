<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratAdmin extends Model
{
    use HasFactory;
    protected $table = 'surat_admin';
    protected $fillable = ['jenis_surat', 'divisi_pembuat', 'divisi_tujuan', 'file_path', 'status_pengajuan'];

    public function getFormattedNomorSuratAttribute()
    {
        $jenis_surat = $this->jenis_surat;
        $id_surat = str_pad($this->id, 3, '0', STR_PAD_LEFT);
        $divisi_pembuat = $this->divisi_pembuat;
        $divisi_tujuan = $this->divisi_tujuan;
        $bulan = date('n', strtotime($this->created_at));
        $tahun = date('Y', strtotime($this->created_at));
        $romawi = ['', 'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'];
        $bulan_romawi = $romawi[$bulan];
        return "{$jenis_surat}/{$id_surat}/{$divisi_pembuat}-{$divisi_tujuan}/{$bulan_romawi}/{$tahun}";
    }
}