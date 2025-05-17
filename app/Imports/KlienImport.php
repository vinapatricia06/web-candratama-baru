<?php
namespace App\Imports;

use App\Models\Klien;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Session;

class KlienImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Cek jika nama_klien kosong
        if (empty($row['nama_klien'])) {
            // Jika nama klien kosong, beri pesan ke session dan lewati baris ini
            Session::flash('duplicate_warning', 'Data pada baris ' . ($row['no_induk'] ?? 'tanpa no_induk') . ' memiliki nama klien yang kosong.');
            return null; // Melewatkan baris ini
        }

        // Cek apakah data dengan 'no_induk' sudah ada di database
        $existingKlien = Klien::where('no_induk', $row['no_induk'])->first();

        if ($existingKlien) {
            // Menambahkan pesan ke session jika data duplikat ditemukan
            Session::flash('duplicate_warning', 'Data dengan no_induk ' . $row['no_induk'] . ' sudah ada.');
            return null; // Melewatkan entri ini tanpa menyimpannya
        }

        // Jika data belum ada, buat dan simpan data baru
        return new Klien([
            'nama_klien' => $row['nama_klien'],
            'no_induk'   => $row['no_induk'],
            'alamat'     => $row['alamat'],
            'no_hp'      => $row['no_hp'],
        ]);
    }
}
