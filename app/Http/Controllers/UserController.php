<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Menampilkan daftar pengguna.
     */
    public function index(Request $request)
    {
        // Jika request adalah AJAX, kembalikan data untuk DataTables
        if ($request->ajax()) {
            $query = DB::connection('sqlsrv')->table('SATUSEHATLOGIN');

            // Tambahkan filter jika KODEGROUP dikirim
            if ($request->filled('kodegroup')) {
                $query->where('KODEGROUP', $request->input('kodegroup'));
            }

            $users = $query->orderBy('Username', 'asc')->get();

            return response()->json(['data' => $users]);
        }

        // Mengambil data group untuk dropdown di modal edit
        $groups = DB::connection('sqlsrv')
                    ->table('RGROUP')
                    ->select('KODEGROUP', 'NAMAGROUP', 'KDUNIT')
                    ->orderBy('NAMAGROUP', 'asc')
                    ->get();
        
        // Jika bukan request AJAX, tampilkan view seperti biasa.
        // Variabel $users tidak perlu dikirim lagi karena akan di-load via AJAX.
        return view('users.index', compact('groups'));
    }

    /**
     * Menyimpan data pengguna baru.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'Username'      => [
                'required',
                'string',
                'max:255',
                // Pastikan Username unik di tabel SATUSEHATLOGIN pada koneksi sqlsrv
                Rule::unique('sqlsrv.SATUSEHATLOGIN', 'Username')
            ],
            'Password'      => 'required|string|max:255',
            'NAMAPEMERIKSA' => 'nullable|string|max:255',
            'Email'         => 'nullable|email|max:255',
            'nik'           => 'nullable|string|max:50',
            'NOPEMERIKSA'   => 'nullable|string|max:50',
            'NOUSER'        => 'nullable|string|max:50',
            'KDUNIT'        => 'nullable|string|max:50',
            'KODEGROUP'     => 'nullable|string|max:50',
            'TEMPLATEID'    => 'nullable|string|max:255',
            'ACCESS'        => 'nullable|string|max:50',
            'IsActive'      => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Mengambil semua data yang divalidasi
            $data = $validator->validated();

            // Sesuai permintaan, password tidak di-hash
            // $data['Password'] = bcrypt($request->Password); // Baris ini jika ingin hashing

            // Menggunakan insertGetId untuk mendapatkan ID dari user yang baru dibuat
            $id = DB::connection('sqlsrv')->table('SATUSEHATLOGIN')->insertGetId($data, 'UserID');

            // Mengambil data user yang baru saja dibuat untuk dikembalikan ke client
            $newUser = DB::connection('sqlsrv')->table('SATUSEHATLOGIN')->where('UserID', $id)->first();


            return response()->json(['message' => 'Pengguna baru berhasil ditambahkan.', 'user' => $newUser], 201);

        } catch (\Exception $e) {
            // Log::error($e->getMessage());
            return response()->json(['message' => 'Gagal menambahkan pengguna baru.'], 500);
        }
    }

    /**
     * Menyimpan banyak pengguna baru sekaligus.
     */
    public function bulkStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'KODEGROUP'  => 'required|string',
            'KDUNIT'     => 'nullable|string',
            'users_data' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $kodeGroup = $request->input('KODEGROUP');
        $kdUnit = $request->input('KDUNIT');
        $usersData = $request->input('users_data');

        // Pecah textarea menjadi baris-baris
        $lines = preg_split("/\r\n|\n|\r/", $usersData);

        $usersToInsert = [];
        $errors = [];
        $successCount = 0;

        foreach ($lines as $index => $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            // Pecah setiap baris menjadi Username, Password, No
            $parts = preg_split('/\s+/', $line, 5); // Memecah berdasarkan spasi/tab, maksimal 5 bagian
            $username = $parts[0] ?? '';
            $password = $parts[1] ?? '';
            $noPemeriksa = $parts[2] ?? '';
            $noUser = $parts[3] ?? '';
            $access = $parts[4] ?? '';

            // Validasi format dasar: pastikan semua 5 kolom ada (meskipun isinya '-')
            if (count($parts) < 5 || empty($username) || empty($password) || empty($access)) {
                $errors[] = "Baris " . ($index + 1) . ": Format salah. Pastikan 5 kolom terisi (Username,Password,No.Pemeriksa,No.User,Role).";
                continue; // Lanjutkan ke baris berikutnya jika format salah
            }
            // Cek duplikasi username sebelum mencoba insert
            $exists = DB::connection('sqlsrv')->table('SATUSEHATLOGIN')->where('Username', $username)->exists();
            if ($exists) {
                $errors[] = "Baris " . ($index + 1) . ": Username '{$username}' sudah ada.";
                continue;
            }

            // Setelah validasi, ubah placeholder '-' menjadi null untuk disimpan ke DB
            $noPemeriksa = ($noPemeriksa === '-') ? '' : $noPemeriksa;
            $noUser = ($noUser === '-') ? '' : $noUser;
            // Anda bisa menambahkan ini untuk kolom lain jika diperlukan

            $usersToInsert[] = [
                'Username'      => $username,
                'Password'      => $password, // Tidak di-hash sesuai permintaan
                'NOPEMERIKSA'   => $noPemeriksa,
                'NOUSER'        => $noUser,
                'ACCESS'        => $access,
                'KODEGROUP'     => $kodeGroup,
                'KDUNIT'        => $kdUnit,
                'IsActive'      => 1, // Default ke aktif
            ];
        }

        // Untuk SQL Server 2000, bulk insert dengan multiple values tidak didukung.
        // Kita harus melakukan insert satu per satu di dalam transaksi.
        DB::connection('sqlsrv')->beginTransaction();
        try {
            if (!empty($usersToInsert)) {
                foreach ($usersToInsert as $user) {
                    DB::connection('sqlsrv')->table('SATUSEHATLOGIN')->insert($user);
                    $successCount++;
                }
            }
            DB::connection('sqlsrv')->commit();
        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            // Tambahkan error ke array untuk ditampilkan ke pengguna
            $errors[] = "Terjadi kesalahan database saat menyimpan data. " . $e->getMessage();
        }

        return response()->json([
            'message' => 'Proses penambahan massal selesai.',
            'success_count' => $successCount,
            'errors' => $errors,
        ], 200);
    }

    /**
     * Memperbarui data pengguna.
     */
    public function update(Request $request, $UserID)
    {
        $validator = Validator::make($request->all(), [
            'Username'      => [
                'required',
                'string',
                'max:50',
                Rule::unique('sqlsrv.SATUSEHATLOGIN', 'Username')->ignore($UserID, 'UserID')
            ],
            'Password'      => 'nullable|string|max:255',
            'NAMAPEMERIKSA' => 'nullable|string|max:255',
            'Email'         => 'nullable|email|max:255',
            'nik'           => 'nullable|string|max:50',
            'NOPEMERIKSA'   => 'nullable|string|max:50',
            'NOUSER'        => 'nullable|string|max:50',
            'KDUNIT'        => 'nullable|string|max:50',
            'KODEGROUP'     => 'nullable|string|max:50',
            'TEMPLATEID'     => 'nullable|string|max:255',
            'ACCESS'        => 'nullable|string|max:50',
            'IsActive'      => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            $updateData = $validator->validated();

            // Hapus password dari array update jika kosong
            if (empty($updateData['Password'])) {
                unset($updateData['Password']);
            }

            $affected = DB::connection('sqlsrv')->table('SATUSEHATLOGIN')
                ->where('UserID', $UserID)
                ->update($updateData);

            if ($affected) {
                // Ambil data terbaru untuk dikembalikan ke client
                $updatedUser = DB::connection('sqlsrv')->table('SATUSEHATLOGIN')->where('UserID', $UserID)->first();
                return response()->json([
                    'message' => 'Data pengguna berhasil diperbarui.',
                    'user' => $updatedUser
                ]);
            }

            return response()->json(['message' => 'Tidak ada perubahan data.'], 200);

        } catch (\Exception $e) {
            // Log error jika perlu: Log::error($e->getMessage());
            return response()->json(['message' => 'Gagal memperbarui data pengguna.'], 500);
        }
    }

    /**
     * Menghapus data pengguna.
     */
    public function destroy($UserID)
    {
        try {
            $user = DB::connection('sqlsrv')->table('SATUSEHATLOGIN')->where('UserID', $UserID);

            if ($user->doesntExist()) {
                return response()->json(['message' => 'Pengguna tidak ditemukan.'], 404);
            }

            $deleted = $user->delete();

            if ($deleted) {
                return response()->json(['message' => 'Pengguna berhasil dihapus.']);
            }

            return response()->json(['message' => 'Gagal menghapus pengguna.'], 500);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }
}