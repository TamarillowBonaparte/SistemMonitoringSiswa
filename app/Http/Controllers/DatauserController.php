<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class DatauserController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $user = User::join('roleuser', 'user.id_roleuser', '=', 'roleuser.id_roleuser')
                    ->select('user.*', 'roleuser.nama_role')
                    ->get();
        return view('datauser', compact('roles', 'user'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:user', // Pastikan nama tabel benar
            'password' => 'required|min:2',
            'role' => 'required|exists:roleuser,id_roleuser' // Validasi role sesuai dengan tabel
        ]);

        try {
            User::create([
                'username' => $request->username,
                //'password' => Crypt::encryptString($request->password),
                'password' => $request->password,
                'id_roleuser' => $request->role // Sesuaikan dengan nama kolom di database
            ]);

            return redirect()->route('datauser')->with('success', 'User berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->route('datauser')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('datauser_edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
{
    $user = User::findOrFail($id);
    $user->username = $request->username;
    if ($request->password) {
        $user->password = $request->password;
    }
    $user->id_roleuser = $request->role;
    $user->save();

    return redirect()->back()->with('success', 'Data user berhasil diperbarui.');
}

public function destroy($id)
{
    $user = User::findOrFail($id);
    $user->delete();

    return response()->json(['success' => true]);
}

}
