<?php

namespace App\Http\Controllers;

use App\Models\ListPelanggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ListPelanggaranController extends Controller
{
    public function index(){
        $listpelanggaran = ListPelanggaran::all();
        return view('listpelanggaran',compact('listpelanggaran'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'skor' => 'required|integer',
            'id_bentukpelanggaran' => 'required|integer',
        ]);

        $data = new ListPelanggaran();
        $data->nama_pelanggaran = $request->nama_pelanggaran;
        $data->skor = $request->skor;
        $data->id_bentukpelanggaran = $request->id_bentukpelanggaran;
        $data->save();

        return redirect()->back()->with('success', 'Pelanggaran berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_pelanggaran' => 'required|string|max:255',
            'skor' => 'required|integer',
            'id_bentukpelanggaran' => 'required|integer',
        ]);

        $data = ListPelanggaran::findOrFail($id);
        $data->nama_pelanggaran = $request->nama_pelanggaran;
        $data->skor = $request->skor;
        $data->id_bentukpelanggaran = $request->id_bentukpelanggaran;
        $data->save();

        return redirect()->back()->with('success', 'Pelanggaran berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $data = ListPelanggaran::findOrFail($id);
        $data->delete();

        return redirect()->back()->with('success', 'Pelanggaran berhasil dihapus.');
    }
}
