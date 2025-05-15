<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ujian;
use App\Models\Kelas;
use App\Models\Pelajaran;

class UjianController extends Controller
{
    public function index()
    {
        $ujian = Ujian::with('kelas', 'pelajaran')->get();
        $kelas = Kelas::all();
        $pelajaran = Pelajaran::all();
        return view('inputjadwalujian', compact('ujian', 'kelas', 'pelajaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required|integer',
            'id_pelajaran' => 'required|integer',
            'jenis_ujian' => 'required|string',
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'ruang_ujian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        Ujian::create([
            'id_kelas' => $request->id_kelas,
            'id_pelajaran' => $request->id_pelajaran,
            'jenis_ujian' => $request->jenis_ujian,
            'tanggal_ujian' => $request->tanggal_ujian,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'ruang_ujian' => $request->ruang_ujian,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('ujian')->with('success', 'Jadwal ujian berhasil ditambahkan!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'id_kelas' => 'required|integer',
            'id_pelajaran' => 'required|integer',
            'jenis_ujian' => 'required|string',
            'tanggal_ujian' => 'required|date',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'ruang_ujian' => 'nullable|string',
            'keterangan' => 'nullable|string',
        ]);

        $ujian = Ujian::findOrFail($id);
        $ujian->update($request->all());

        return redirect()->route('ujian')->with('success', 'Jadwal ujian berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $ujian = Ujian::findOrFail($id);
        $ujian->delete();

        return redirect()->route('ujian')->with('success', 'Jadwal ujian berhasil dihapus!');
    }
}
