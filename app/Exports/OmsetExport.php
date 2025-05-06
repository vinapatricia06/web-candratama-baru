<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;

class OmsetExport implements FromCollection, WithHeadings, WithMapping
{
    protected $omsets;

    public function __construct($omsets)
    {
        $this->omsets = $omsets;
    }

    public function collection()
    {
        return $this->omsets; // Menggunakan data omset yang sudah difilter di controller
    }

    public function headings(): array
    {
        return [
            'No', // Kolom pertama adalah nomor urut
            'Tanggal',
            'Nama Klien',
            'Alamat',
            'Project',
            'Sumber Lead',
            'Nominal'
        ];
    }

    public function map($omset): array
    {
        // Tambahkan nomor urut dengan $loop->iteration
        static $no = 1; // Static untuk menjaga nomor urut berjalan di seluruh data
        return [
            $no++, // Menambahkan nomor urut
            $omset->tanggal,
            $omset->nama_klien,
            $omset->alamat,
            $omset->project,
            $omset->sumber_lead,
            'Rp ' . number_format($omset->nominal, 2, ',', '.') // Format nominal dengan mata uang
        ];
    }
}
