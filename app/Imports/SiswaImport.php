<?php
namespace App\Imports;

use App\Models\Siswa;
use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class SiswaImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        // Skip heading
        $rows->shift();

        // Mapping manual nama_kelas -> id_kelas
        $kelasMapping = [
            'X TKR 1' => 9,
            'X TKR 2' => 10,
            'XI TKR 1' => 11,
            'XI TKR 2' => 12,
            'XII TKR 1' => 13,
            'XII TKR 2' => 14,
        ];

        foreach ($rows as $index => $row) {
            try {
                $nama_kelas = strtoupper(trim($row[15] ?? '')); // Kolom Q

                if (empty($nama_kelas)) {
                    Log::warning("Row #" . ($index + 2) . " kosong nama_kelas. Skip.");
                    continue;
                }

                if (!isset($kelasMapping[$nama_kelas])) {
                    Log::warning("Mapping id_kelas tidak ditemukan untuk nama_kelas='$nama_kelas'. Skipping row #" . ($index + 2));
                    continue;
                }

                $id_kelas = $kelasMapping[$nama_kelas];

                // Data siswa
                $nama_siswa = $row[0];
                $nisn = $row[1];
                $no_orangtua = $row[2];
                $jenis_kelamin = $row[3];
                $tempat_tgl_lahir = $row[4];
                $alamat = $row[5];
                $nama_ayah = $row[6];
                $pendidikan_ayah = $row[7];
                $pekerjaan_ayah = $row[8];
                $nama_ibu = $row[9];
                $pendidikan_ibu = $row[10];
                $pekerjaan_ibu = $row[11];
                $nama_wali = $row[12];
                $pendidikan_wali = $row[13];
                $pekerjaan_wali = $row[14];

                $siswa = Siswa::where('nama_siswa', $nama_siswa)->first();

                $siswaData = [
                    'nama_siswa' => $nama_siswa,
                    'nisn' => $nisn,
                    'no_orangtua' => $no_orangtua,
                    'jenis_kelamin' => $jenis_kelamin,
                    'tempat_tgl_lahir' => $tempat_tgl_lahir,
                    'alamat' => $alamat,
                    'nama_ayah' => $nama_ayah,
                    'pendidikan_ayah' => $pendidikan_ayah,
                    'pekerjaan_ayah' => $pekerjaan_ayah,
                    'nama_ibu' => $nama_ibu,
                    'pendidikan_ibu' => $pendidikan_ibu,
                    'pekerjaan_ibu' => $pekerjaan_ibu,
                    'nama_wali' => $nama_wali ?: null,
                    'pendidikan_wali' => $pendidikan_wali ?: null,
                    'pekerjaan_wali' => $pekerjaan_wali ?: null,
                    'id_kelas' => $id_kelas,
                ];

                if ($siswa) {
                    $siswa->update($siswaData);
                    Log::info("Updated existing student: {$nama_siswa}");

                    User::updateOrCreate(
                        ['username' => $nisn, 'id_roleuser' => 3],
                        ['password' => Hash::make($nisn)]
                    );

                    User::updateOrCreate(
                        ['username' => $no_orangtua, 'id_roleuser' => 4],
                        ['password' => Hash::make($no_orangtua)]
                    );
                } else {
                    $siswaBaru = Siswa::create($siswaData);
                    Log::info("Created new student: {$nama_siswa} with ID: {$siswaBaru->id}");

                    User::create([
                        'username' => $siswaBaru->nisn,
                        'password' => $siswaBaru->nisn,
                        'id_roleuser' => 3,
                    ]);

                    User::create([
                        'username' => $siswaBaru->no_orangtua,
                        'password' => $siswaBaru->no_orangtua,
                        'id_roleuser' => 4,
                    ]);
                }
            } catch (\Exception $e) {
                Log::error("Error processing row #" . ($index + 2) . ": " . $e->getMessage());
                Log::error($e->getTraceAsString());
            }
        }
    }
}
