<?php
namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Jurusan;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class DataGuruController extends Controller
{
    public function index()
    {
        $kelas = Kelas::all();
        $guru = Guru::all();
        $jurusan = Jurusan::all();

        return view('dataguru', compact('kelas', 'guru', 'jurusan'));
    }


    public function tambahGuru(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'nama_guru' => 'required',
            'jenis_kelamin' => 'required',
            'agama' => 'required',
            'nip' => 'required|nullable',
            'no_telp_guru' => 'required',
        ]);


        Guru::create($request->only([
            'nama_guru',
            'jenis_kelamin',
            'agama',
            'nip',
            'no_telp_guru'
        ]));
        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function edit(Request $request, $d)
    {
        if ($request->isMethod('post')) {
            $g = $request->all();
            Guru::where(['id_guru' => $d])->update([
                'nama_guru' => $g['nama_guru'],
                'jenis_kelamin' => $g['jenis_kelamin'],
                'agama' => $g['agama'],
                'nip' => $g['nip'],
                'no_telp_guru' => $g['no_telp_guru'],
            ]);
            return redirect()->back()->with('success', 'Data guru berhasil diubah.');
        }
    }

    public function storeKelas(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama_kelas' => 'required|string|max:255|unique:kelas,nama_kelas',
            'jenjang' => 'required|string|max:255',
            'jurusan' => 'required|string|max:255',
            'wali_kelas' => 'required|string|max:255',
        ]);

        // Simpan ke database
        $kelas = new Kelas();
        $kelas->jenjang = $request->jenjang;
        $kelas->id_jurusan = $request->jurusan;
        $kelas->nama_kelas = $request->nama_kelas;
        $kelas->wali_kelas = $request->wali_kelas; // Perbaikan disini
        $kelas->save();

        // Redirect dengan pesan sukses
        return redirect()->back()->with('success', 'Kelas berhasil ditambahkan.');
    }

    public function editkelas(Request $request, $d)
    {
        if ($request->isMethod('post')) {
            $k = $request->all();
            Kelas::where(['id_kelas' => $d])->update([
                'nama_kelas' => $k['nama_kelas'],
                'jenjang' => $k['jenjang'],
                'id_jurusan' => $k['id_jurusan'],
                'wali_kelas' => $k['wali_kelas'], // Perbaikan disini
            ]);
            return redirect()->back()->with('success', 'Data kelas berhasil diubah.');
        }
    }

    public function delete($id = null)
    {
        Guru::where('id_guru', $id)->delete();

        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }

    public function deletekelas($id_kelas = null)
    {
        Kelas::where('id_kelas', $id_kelas)->delete();

        return redirect()->back()->with('success', 'Data kelas berhasil dihapus.');
    }


    public function storeJurusan(Request $request)
{
    $request->validate([
        'nama_jurusan' => 'required|string|max:255|unique:jurusan,nama_jurusan',
    ]);

    Jurusan::create([
        'nama_jurusan' => $request->nama_jurusan,
    ]);

    return redirect()->back()->with('success', 'Jurusan berhasil ditambahkan.');
}

public function updateJurusan(Request $request, $id)
{
    $request->validate([
        'nama_jurusan' => [
            'required',
            'string',
            'max:255',
            Rule::unique('jurusan', 'nama_jurusan')->ignore($id, 'id_jurusan')
        ]
    ]);

    $jurusan = Jurusan::findOrFail($id);
    $jurusan->nama_jurusan = $request->nama_jurusan;
    $jurusan->save();

    return redirect()->back()->with('success', 'Jurusan berhasil diperbarui.');
}

public function deleteJurusan($id)
{
    Log::info('Menghapus jurusan dengan ID: ' . $id);
    Jurusan::where('id_jurusan', $id)->delete();
    return redirect()->back()->with('success', 'Jurusan berhasil dihapus.');
}


}
