<?php

namespace App\Http\Controllers;

use App\Models\JadwalPelajaran;
use App\Models\Kelas;
use App\Models\Kodemapel;
use App\Models\NilaiAkhir;
use App\Models\Siswa;
use App\Models\KodePembelajaran;
use Illuminate\Http\Request;

class NilaiAkhirController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // Cek apakah user adalah admin
    if (session('nama_role') === 'Admin' || optional($user)->roleuser->nama_role === 'Admin') {
        $kelas = Kelas::all();
        $kodePembelajaran = Kodemapel::with(['pelajaran', 'guru'])->get();
    } else {
        $idGuru = session('id_guru');

        // Ambil kelas yang diajar oleh guru
        $kelas = JadwalPelajaran::join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
            ->join('kelas', 'jadwal_pelajaran.id_kelas', '=', 'kelas.id_kelas')
            ->where('kode_pembelajaran.id_guru', $idGuru)
            ->select('kelas.*')
            ->distinct()
            ->get();

        // Ambil kode pembelajaran sesuai guru
        $kodePembelajaran = Kodemapel::with(['pelajaran', 'guru'])
            ->where('id_guru', $idGuru)
            ->get();
    }

    $siswa = Siswa::all();
    $nilai = NilaiAkhir::with(['siswa', 'kodePembelajaran.pelajaran', 'kodePembelajaran.guru'])->get();

    return view('inputnilaiakhir', compact('siswa', 'kodePembelajaran', 'nilai', 'kelas'));
}


    public function store(Request $request)
{
    // dd($request)::all();
    $request->validate([
        'id_siswa' => 'required|exists:siswa,id_siswa',
        'id_kodepembelajaran' => 'required|exists:kode_pembelajaran,id_kodepembelajaran',
        'semester' => 'required|integer',
        'nilai' => 'required|numeric|min:0|max:100'
    ]);

    $nilaiAkhir = new NilaiAkhir();
    $nilaiAkhir->id_siswa = $request->id_siswa;
    $nilaiAkhir->id_kodepembelajaran = $request->id_kodepembelajaran;
    $nilaiAkhir->semester = $request->semester;
    $nilaiAkhir->nilai = $request->nilai;
    $nilaiAkhir->created_at = now();
    $nilaiAkhir->save();

    return redirect()->back()->with('success', 'Data nilai akhir berhasil ditambahkan.');
}


    public function getSiswaByKelas($id)
    {
        $siswa = Siswa::where('id_kelas', $id)->get(['id_siswa', 'nama_siswa']);
        return response()->json($siswa);
    }


    public function getMapelByKelas($id_kelas)
{
    $user = auth()->user();  // Ambil user yang sedang login

    if (session('nama_role') === 'Admin' || optional($user)->roleuser->nama_role === 'Admin') {
        // Ambil semua kode pembelajaran berdasarkan kelas dari jadwal
        $data = JadwalPelajaran::with(['kodemapel.pelajaran', 'kodemapel.guru'])
            ->where('id_kelas', $id_kelas)
            ->get()
            ->map(function ($item) {
                return [
                    'id_kodepembelajaran' => $item->kodemapel->id_kodepembelajaran ?? null,
                    'nama_pelajaran' => $item->kodemapel->pelajaran->nama_pelajaran ?? 'Tidak Ditemukan',
                    'nama_guru' => $item->kodemapel->guru->nama_guru ?? 'Tidak Ditemukan',
                ];
            })
            ->filter(function ($item) {
                return $item['id_kodepembelajaran'] !== null;
            })
            ->values();
    } else {
        $id_guru = session('id_guru');
        $data = Kodemapel::with(['pelajaran', 'guru', 'jadwal' => function ($q) use ($id_kelas) {
            $q->where('id_kelas', $id_kelas);
        }])
        ->where('id_guru', $id_guru)
        ->get()
        ->filter(function ($item) {
            return $item->jadwal->isNotEmpty();
        })
        ->map(function ($item) {
            return [
                'id_kodepembelajaran' => $item->id_kodepembelajaran,
                'nama_pelajaran' => $item->pelajaran->nama_pelajaran ?? 'Tidak Ditemukan',
                'nama_guru' => $item->guru->nama_guru ?? 'Tidak Ditemukan',
            ];
        })
        ->values();
    }

    return response()->json($data);
}




    public function destroy($id)
    {
        $nilai = NilaiAkhir::findOrFail($id);
        $nilai->delete();

        return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
    }

    public function update(Request $request, $id)
{
    $nilai = NilaiAkhir::findOrFail($id);
    $nilai->update([
        'id_siswa' => $request->id_siswa,
        'id_kodepembelajaran' => $request->id_kodepembelajaran,
        'semester' => $request->semester,
        'nilai' => $request->nilai,
    ]);

    return redirect()->route('nilaiakhir')->with('success', 'Nilai berhasil diupdate.');
}





}
