<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginPageController extends Controller
{
    public function index()
    {
        // If user is already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('loginpage');
    }

    /**
     * Handle the login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    // public function authenticate(Request $request)
    // {
    //     $request->validate([
    //         'username' => 'required',
    //         'password' => 'required',
    //     ]);

    //     // Cari user berdasarkan username (yang berupa no_telp)
    //     $user = User::where('username', $request->username)->first();

    //     // Cek user dan password (gunakan hash jika sudah aman)
    //     if ($user && $request->password === $user->password) {
    //         // Cek apakah user ini juga seorang guru berdasarkan no_telp
    //         $guru = Guru::where('no_telp_guru', $user->username)->first();

    //         if ($guru) {
    //             // Login dan simpan id_guru & nama ke session
    //             Auth::login($user);
    //             $request->session()->regenerate();

    //             // Simpan info guru ke session (jika ada)
    //             session([
    //                 'id_guru' => $guru->id_guru,
    //                 'nama_guru' => $guru->nama_guru,
    //             ]);

    //             return redirect()->intended('dashboard');
    //         } else {
    //             return back()->with('error', 'No. Telepon tidak terdaftar sebagai guru.');
    //         }
    //     }

    //     return back()->with('error', 'Username atau password salah!');
    // }

        public function authenticate(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        $user = User::where('username', $request->username)->first();

        if ($user && $request->password === $user->password) {
            Auth::login($user);
            $request->session()->regenerate();

            // Ambil role user (dari relasi)
            $roleId = $user->roleuser->id_roleuser;

            // Cek jika role-nya adalah GURU (id_roleuser = 2)
            if ($roleId == 2) {
                // Ambil data guru berdasarkan username = no_telepon
                $guru = \App\Models\Guru::where('no_telp_guru', $user->username)->first();

                if ($guru) {
                    session([
                        'id_guru' => $guru->id_guru,
                        'nama_guru' => $guru->nama_guru,
                    ]);
                } else {
                    // Logout user kalau gagal cocokkan guru
                    Auth::logout();
                    return back()->with('error', 'No. Telepon tidak terdaftar sebagai guru.');
                }
            } else {
                // Kalau admin atau role lain, tetap simpan sesuatu biar seragam
                session([
                    'id_guru' => null,
                    'nama_guru' => $user->username,
                    'nama_role' => $user->roleuser->nama_role,
                ]);
            }

            return redirect()->intended('dashboard');
        }

        return back()->with('error', 'Username atau password salah!');
    }


    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }
}
