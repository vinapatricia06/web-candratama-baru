<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grafik extends Model
{
    use HasFactory;

    protected $table = 'grafiks';
    protected $primaryKey = 'id_grafik';
    protected $fillable = ['bulan', 'tahun', 'total_omset'];
}
