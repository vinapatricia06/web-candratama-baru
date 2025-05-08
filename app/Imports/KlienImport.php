<?php

namespace App\Imports;

use App\Models\Klien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class KlienImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Klien([
            'nama_klien' => $row['nama_klien'],
            'no_induk'   => $row['no_induk'],
            'alamat'     => $row['alamat'],
            'no_hp'      => $row['no_hp'],
        ]);
    }
}
