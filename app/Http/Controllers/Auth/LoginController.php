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

        // Menggunakan parameter binding (?) untuk mencegah SQL Injection
        // Secara eksplisit memilih koneksi 'sqlsrv'
        $userObject = DB::connection('sqlsrv')->selectOne(
            "SELECT * FROM SATUSEHATLOGIN WHERE Username = ? AND Password = ? AND IsActive = 1",
            [$request->Username, $request->Password]
        );

        // Jika user ditemukan
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

        // Jika gagal, kirim pesan error menggunakan ValidationException
        throw ValidationException::withMessages([
            'Username' => ['Username atau Password salah.'],
        ])->redirectTo(route('login'));
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