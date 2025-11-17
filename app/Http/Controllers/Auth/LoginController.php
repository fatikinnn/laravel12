<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Menampilkan form login.
     */
    public function showLoginForm()
    {
        // Jika user sudah login, langsung redirect ke dashboard
        if (session()->has('user')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    /**
     * Menangani permintaan login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'Username' => 'required|string',
            'Password' => 'required|string',
        ]);

        // 1. Cek apakah user ada berdasarkan Username
        $userObject = DB::connection('sqlsrv')->selectOne(
            "SELECT * FROM SATUSEHATLOGIN WHERE Username = ?",
            [$request->Username]
        );

        // Jika user tidak ditemukan sama sekali
        if (!$userObject) {
            $errorMessage = 'Username tidak ditemukan. Silakan hubungi IT.';
            if ($request->wantsJson()) {
                return response()->json(['message' => $errorMessage, 'errors' => ['Username' => [$errorMessage]]], 422);
            }
            return back()->withInput($request->only('Username'))->withErrors(['Username' => $errorMessage]);
        }

        // 2. Jika user ditemukan, cek apakah password cocok dan user aktif
        $userObject = DB::connection('sqlsrv')->selectOne(
            "SELECT * FROM SATUSEHATLOGIN WHERE Username = ? AND Password = ? AND IsActive = 1",
            [$request->Username, $request->Password]
        );

        // Jika user ditemukan dengan password yang benar dan aktif
        if ($userObject) {
            // Konversi ke array asosiatif untuk menghindari masalah case-sensitivity
            $userArray = (array) $userObject;

            // Normalisasi: Ubah semua keys menjadi lowercase untuk konsistensi di seluruh aplikasi
            $user = [];
            foreach ($userArray as $key => $value) {
                $user[strtolower($key)] = is_string($value) ? trim($value) : $value;
            }

            // Regenerasi session untuk keamanan
            $request->session()->regenerate();
            // Simpan data user sebagai array ke dalam session
            $request->session()->put('user', $user);

            // Jika ini adalah request AJAX, kirim respons JSON
            if ($request->wantsJson()) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Login Berhasil! Selamat Datang, ' . ($user['namapemeriksa'] ?? $user['username']),
                    'redirect_url' => route('dashboard')
                ]);
            }

            // Jika bukan AJAX, lakukan redirect seperti biasa
            return redirect()->intended('dashboard')
                ->with('swal-success', 'Login Berhasil! Selamat Datang, ' . ($user['namapemeriksa'] ?? $user['username']));
        }

        // 3. Jika user ada tapi password salah atau tidak aktif
        $errorMessage = 'Password salah atau akun Anda tidak aktif.';
        if ($request->wantsJson()) {
            // Kita kirim error ke field Password agar fokus input bisa dipindah ke sana
            return response()->json(['message' => $errorMessage, 'errors' => ['Password' => [$errorMessage]]], 422);
        }

        return back()->withInput($request->only('Username'))->withErrors(['Password' => $errorMessage]);
    }

    /**
     * Menangani permintaan logout.
     */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}