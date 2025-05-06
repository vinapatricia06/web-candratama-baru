<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Omset extends Model
{
    use HasFactory;
    protected $table = 'omsets';
    protected $primaryKey = 'id_omset';
    protected $fillable = ['tanggal','no_induk', 'nama_klien', 'alamat', 'project','sumber_lead', 'nominal'];
}
