<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Hari;
use App\Models\Jampelajaran;
use App\Models\Klasifikasi_Hari;
use App\Models\Kodemapel;
use App\Models\Pelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataPelajaranController extends Controller
{
    public function index()
    {
        $hari = Hari::all();
        $guru = Guru::all();
        $kode_pelajaran = Kodemapel::all();
        // $jam_pelajaran = Jampelajaran::all();
        $jam_pelajaran = DB::table('jam_pelajaran')->join('klasifikasi_hari', 'jam_pelajaran.id_klasifikasi_hari', '=', 'klasifikasi_hari.id')->select('jam_pelajaran.*', 'klasifikasi_hari.hari as nama_hari')->get();


        $pelajaran = Pelajaran::all();
        return view('datapelajaran', compact('pelajaran', 'jam_pelajaran', 'kode_pelajaran', 'guru', 'hari', ));
    }

    public function storekodepelajaran(Request $request)
    {
        //  dd($request);
        $request->validate([
            'kode_mapel' => 'required',
            'id_pelajaran' => 'required',
            'id_guru' => 'required',
        ]);

        Kodemapel::create([
            'kode_mapel' => $request->kode_mapel,
            'id_pelajaran' => $request->id_pelajaran,
            'id_guru' => $request->id_guru,
        ]);

        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function editkodepelajaran(Request $request, $d)
    {
        $request->validate([
            'kode_mapel' => 'required',
            'id_pelajaran' => 'required',
            'id_guru' => 'required',
        ]);

        Kodemapel::where('id_kodepembelajaran', $d)->update([
            'kode_mapel' => $request->kode_mapel,
            'id_pelajaran' => $request->id_pelajaran,
            'id_guru' => $request->id_guru,
        ]);
        return redirect()->back()->with('success', 'Data guru berhasil diubah.');
    }
    public function deletekodepelajaran($id = null)
    {
        Kodemapel::where('id_kodepembelajaran', $id)->delete();

        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }

    public function storejampelajaran(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'jamke' => 'required',
            'jam_pelajaran' => 'required',
            'id_hari' => 'required',
        ]);

        Jampelajaran::create([
            'jamke' => $request->jamke,
            'jam_range' => $request->jam_pelajaran,
            'id_klasifikasi_hari' => $request->id_hari,
        ]);

        return redirect()->back()->with('success', 'Data jam pelajaran berhasil ditambahkan.');
    }

    public function editJamPelajaran(Request $request, $d)
{
    $request->validate([
        'jamke' => 'required',
        'jam_pelajaran' => 'required',
        'id_hari' => 'required',
    ]);

    Jampelajaran::where('id_jam_pelajaran', $d)->update([
        'jamke' => $request->jamke,
        'jam_range' => $request->jam_pelajaran, // Sesuai nama field hidden
        'id_klasifikasi_hari' => $request->id_hari,
    ]);

    return redirect()->back()->with('success', 'Data jam pelajaran berhasil diubah.');
}


    public function deletejampelajaran($id = null)
    {
        Jampelajaran::where('id_jam_pelajaran', $id)->delete();

        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }



    public function storepelajaran(Request $request)
    {
        $request->validate([
            'nama_pelajaran' => 'required|string|max:255',
        ]);

        if (Pelajaran::where('nama_pelajaran', $request->nama_pelajaran)->exists()) {
            return redirect()->back()->with('error', 'Data sudah ada!');
        }

        Pelajaran::create([
            'nama_pelajaran' => $request->nama_pelajaran,
        ]);

        return redirect()->back()->with('success', 'Data pelajaran berhasil ditambahkan.');
    }

    public function editPelajaran(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_pelajaran' => 'required|string|max:255',
        ]);

        Pelajaran::where('id_pelajaran', $id)->update([
            'nama_pelajaran' => $validated['nama_pelajaran'],
        ]);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diubah.');
    }

    public function deletepelajaran($id = null)
    {
        Pelajaran::where('id_pelajaran', $id)->delete();

        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }

    public function storehari(Request $request)
    {
        $request->validate([
            'nama_hari' => 'required|string|max:255',
            'status' => 'required',
        ]);

        Hari::create($request->all());

        return redirect()->back()->with('success', 'Data pelajaran berhasil ditambahkan.');
    }

    public function destroyhari($id)
    {
        // Hapus data hari berdasarkan ID
        $hari = Hari::findOrFail($id);
        $hari->delete();

        // Redirect balik dengan pesan sukses
        return redirect()->back()->with('success', 'Data hari berhasil dihapus.');
    }
    public function update(Request $request, $id)
    {
        // Validasi data
        $validated = $request->validate([
            'nama_hari' => 'required|string|max:50',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        // Cari dan update data hari
        $hari = Hari::findOrFail($id);
        $hari->update($validated);

        // Redirect balik dengan pesan sukses
        return redirect()->back()->with('success', 'Data hari berhasil diperbarui.');
    }
}
