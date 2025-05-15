<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SiswaExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Siswa::with('kelas')->get()->map(function ($siswa) {
            return [
                'nama_siswa' => $siswa->nama_siswa,
                'nisn' => $siswa->nisn,
                'no_orangtua' => $siswa->no_orangtua,
                'jenis_kelamin' => $siswa->jenis_kelamin,
                'tempat_tgl_lahir' => $siswa->tempat_tgl_lahir,
                'alamat' => $siswa->alamat,
                'nama_ayah' => $siswa->nama_ayah,
                'pendidikan_ayah' => $siswa->pendidikan_ayah,
                'pekerjaan_ayah' => $siswa->pekerjaan_ayah,
                'nama_ibu' => $siswa->nama_ibu,
                'pendidikan_ibu' => $siswa->pendidikan_ibu,
                'pekerjaan_ibu' => $siswa->pekerjaan_ibu,
                'nama_wali' => $siswa->nama_wali,
                'pendidikan_wali' => $siswa->pendidikan_wali,
                'pekerjaan_wali' => $siswa->pekerjaan_wali,
                'kelas' => $siswa->kelas ? $siswa->kelas->jenjang . '  ' . $siswa->kelas->nama_kelas : '',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'NISN',
            'No Orangtua',
            'Jenis Kelamin',
            'Tempat Tgl Lahir',
            'Alamat',
            'Nama Ayah',
            'Pendidikan Ayah',
            'Pekerjaan Ayah',
            'Nama Ibu',
            'Pendidikan Ibu',
            'Pekerjaan Ibu',
            'Nama Wali',
            'Pendidikan Wali',
            'Pekerjaan Wali',
            'Kelas',
        ];
    }
}
