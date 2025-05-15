<?php

namespace App\Http\Controllers;

use App\Exports\SiswaExport;
use App\Imports\SiswaImport;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class DataSiswaOrtuController extends Controller
{
    public function index()
    {
        $siswa = Siswa::all();
        $kelas = Kelas::all();

        return view('dataortudansiswa', compact('siswa', 'kelas'));
    }

    public function storesiswa(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama_siswa' => 'required',
            'nisn' => 'required|unique:siswa,nisn',
            'foto_siswa' => 'nullable|mimes:jpeg,png,jpg',
            'no_orangtua' => 'required',
            'jenis_kelamin' => 'required',
            'tempat_tgl_lahir' => 'required',
            'alamat' => 'required',
            'nama_ayah' => 'required',
            'pendidikan_ayah' => 'required',
            'pekerjaan_ayah' => 'required',
            'nama_ibu' => 'required',
            'pendidikan_ibu' => 'required',
            'pekerjaan_ibu' => 'required',
            'nama_wali' => 'required',
            'pendidikan_wali' => 'required',
            'pekerjaan_wali' => 'required',
            'id_kelas' => 'required',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto_siswa')) {
            $foto = $request->file('foto_siswa');
            $namaFoto = time() . '.' . $foto->getClientOriginalExtension();
            $foto->move(public_path('uploads/siswa'), $namaFoto);
            $data['foto_siswa'] = $namaFoto;
        }
        else {
            $data['foto_siswa'] = null; // atau bisa diisi default foto: 'default.jpg'
        }

        $siswa = Siswa::create($data);

        User::create([
            'username' => $siswa->nisn,
            'password' => $siswa->nisn,
            'id_roleuser' => 3,
        ]);

        User::create([
            'username' => $siswa->no_orangtua,
            'password' => bcrypt($siswa->no_orangtua),
            'id_roleuser' => 4,
        ]);

        return redirect()->back()->with('success', 'Data siswa dan akun berhasil ditambahkan.');
        return redirect()->back()->with('error', 'Gagal menambahkan data siswa.');
    }

    public function edit($id)
    {
        $siswa = Siswa::findOrFail($id);
        $kelas = Kelas::all();
        return view('siswa.edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
{
    $siswa = Siswa::findOrFail($id);

    $validatedData = $request->validate([
        'nama_siswa' => 'required',
        'nisn' => 'required|numeric',
        'no_orangtua' => 'required',
        'jenis_kelamin' => 'required',
        'tempat_tgl_lahir' => 'required',
        'alamat' => 'required',
        'nama_ayah' => 'required',
        'pendidikan_ayah' => 'required',
        'pekerjaan_ayah' => 'required',
        'nama_ibu' => 'required',
        'pendidikan_ibu' => 'required',
        'pekerjaan_ibu' => 'required',
        'nama_wali' => 'required',
        'pendidikan_wali' => 'required',
        'pekerjaan_wali' => 'required',
        'id_kelas' => 'required',
    ]);

    // Simpan nilai lama
    $oldNisn = $siswa->nisn;
    $oldNoOrangtua = $siswa->no_orangtua;

    // Update siswa
    $siswa->update($validatedData);

    // Handle foto
    if ($request->hasFile('foto_siswa')) {
        if ($siswa->foto_siswa && file_exists(public_path('uploads/siswa/' . $siswa->foto_siswa))) {
            unlink(public_path('uploads/siswa/' . $siswa->foto_siswa));
        }

        $file = $request->file('foto_siswa');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/siswa'), $fileName);
        $siswa->foto_siswa = $fileName;
        $siswa->save();
    }

    // Update User siswa kalau nisn berubah
    $userSiswa = User::where('username', $oldNisn)->where('id_roleuser', 3)->first();
    if ($userSiswa) {
        $userSiswa->update([
            'username' => $siswa->nisn, 
            'password' => $siswa->nisn,
        ]);
    }

    // Update User orangtua kalau no_orangtua berubah
    $userOrangtua = User::where('username', $oldNoOrangtua)->where('id_roleuser', 4)->first();
    if ($userOrangtua) {
        $userOrangtua->update([
            'username' => $siswa->no_orangtua,
            'password' => $siswa->no_orangtua,
        ]);
    }

    return redirect()->route('datasiswaortu')->with('success', 'Data siswa dan akun berhasil diperbarui');
}


    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        // Delete photo if exists
        if ($siswa->foto_siswa && file_exists(public_path('uploads/siswa/' . $siswa->foto_siswa))) {
            unlink(public_path('uploads/siswa/' . $siswa->foto_siswa));
        }

        $siswa->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,xls',
        ]);

        Excel::import(new SiswaImport(), $request->file('file'));

        return redirect()->back()->with('success', 'Data siswa berhasil diimpor.');
    }

    public function exportExcel()
    {
        return Excel::download(new SiswaExport(), 'data_siswa.xlsx');
    }

    public function destroyAll()
    {
        // Hapus semua foto dulu jika ada
        $siswas = \App\Models\Siswa::all();
        foreach ($siswas as $siswa) {
            if ($siswa->foto_siswa && file_exists(public_path('uploads/siswa/' . $siswa->foto_siswa))) {
                unlink(public_path('uploads/siswa/' . $siswa->foto_siswa));
            }
        }

        // Hapus semua data siswa
        \App\Models\Siswa::query()->delete();

        return redirect()->back()->with('success', 'Semua data siswa berhasil dihapus.');
    }
}
