<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tugas;
use App\Models\Kelas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TugasController extends Controller
{

    public function index()
{
    // dd(auth()->user()->roleuser->nama_role ?? 'User' );

    $user = Auth::user();

    if (session('nama_role') === 'Admin' || optional($user)->roleuser->nama_role === 'Admin') {
        $kelas = Kelas::all();
    } else {
        $idGuru = session('id_guru');

        $kelas = DB::table('jadwal_pelajaran')
            ->join('kode_pembelajaran', 'jadwal_pelajaran.id_kodepembelajaran', '=', 'kode_pembelajaran.id_kodepembelajaran')
            ->join('kelas', 'jadwal_pelajaran.id_kelas', '=', 'kelas.id_kelas')
            ->where('kode_pembelajaran.id_guru', $idGuru)
            ->select('kelas.*')
            ->distinct()
            ->get();
    }

    $tugas = Tugas::with('kelas')->get();

    return view('tugas', compact('kelas', 'tugas'));
}


public function create()
{
    $user = session('id_guru');

    if ($user->roleuser->nama_role === 'Admin') {
        $kelas = Kelas::all();
    } else {
        $idGuru = $user->id_guru;

        $kodePembelajaranIds = DB::table('kode_pembelajaran')
            ->where('id_guru', $idGuru)
            ->pluck('id_kodepembelajaran');

        $kelasIds = DB::table('jadwal_pembelajaran')
            ->whereIn('id_kodepembelajaran', $kodePembelajaranIds)
            ->pluck('id_kelas');

        $kelas = Kelas::whereIn('id_kelas', $kelasIds)->get();
    }

    return view('tugas.create', compact('kelas'));
}
    public function store(Request $request)
    {
        $request->validate([
            'id_kelas' => 'required',
            'judul_tugas' => 'required',
            'deadline' => 'required|date',
            'file_tugas' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'link_tugas' => 'nullable|url',
        ]);
        // dd($request->all());
        $filePath = null;
        if ($request->hasFile('file_tugas')) {
            $filePath = $request->file('file_tugas')->store('uploads/tugas', 'public');
        }

        Tugas::create([
            'id_guru' => Auth::user()->id_guru,
            'id_kelas' => $request->id_kelas,
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
            'file_tugas' => $filePath,
            'link_tugas' => $request->link_tugas,
        ]);

        return redirect()->route('tugas')->with('success', 'Tugas berhasil ditambahkan');
    }

    public function update(Request $request, $id)
    {
        $tugas = Tugas::findOrFail($id);
        $filePath = $tugas->file_tugas;

        $request->validate([
            'id_kelas' => 'required',
            'judul_tugas' => 'required',
            'deadline' => 'required|date',
            'file_tugas' => 'nullable|file|mimes:pdf,jpg,png,jpeg|max:2048',
            'link_tugas' => 'nullable|url',
        ]);

        if ($request->hasFile('file_tugas')) {
            $filePath = $request->file('file_tugas')->store('uploads/tugas', 'public');
        }

        $tugas->update([
            'id_kelas' => $request->id_kelas,
            'judul_tugas' => $request->judul_tugas,
            'deskripsi' => $request->deskripsi,
            'deadline' => $request->deadline,
            'file_tugas' => $filePath,
            'link_tugas' => $request->link_tugas,
        ]);

        return redirect()->route('tugas')->with('success', 'Tugas berhasil diperbarui');
    }

    public function destroy($id)
    { 
        $tugas = Tugas::findOrFail($id);
        $tugas->delete();

        return redirect()->back()->with('success', 'Tugas berhasil dihapus.');
    }
}
