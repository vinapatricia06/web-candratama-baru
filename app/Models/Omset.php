<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Omset extends Model
{
    use HasFactory;
    protected $table = 'omsets';
    protected $primaryKey = 'id_omset';
    protected $fillable = ['tanggal', 'progress_projects_id', 'sumber_lead', 'nominal', 'metode_pembayaran', 'catatan_pembayaran'];

    public function project()
    {
        return $this->belongsTo(ProgressProject::class, 'progress_projects_id', 'id');
    }
}
