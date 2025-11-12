<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;  

class ProfileController extends Controller
{
    /**
     * Mengambil data profil pengguna saat ini.
     */
    public function show(Request $request)
    {
        if (!$request->session()->has('user')) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $request->session()->get('user');

        // Mengambil data terbaru dari database
        $dbUser = DB::connection('sqlsrv')->selectOne(
            "SELECT Username, NIK, Email, NamaPemeriksa FROM SATUSEHATLOGIN WHERE Username = ?",
            [$user['username']]
        );

        if (!$dbUser) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'username' => trim($dbUser->Username),
            'nik' => trim($dbUser->NIK),
            'email' => trim($dbUser->Email),
            'namapemeriksa' => trim($dbUser->NamaPemeriksa),
        ]);
    }

    /**
     * Memperbarui data profil pengguna.
     */
    public function update(Request $request)
{
    if (!$request->session()->has('user')) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $user = $request->session()->get('user');
    $username = $user['username'];

    $validator = Validator::make($request->all(), [
        'namapemeriksa' => 'nullable|string|max:255',
        'email' => ['nullable', 'email', 'max:255', Rule::unique('sqlsrv.SATUSEHATLOGIN', 'Email')->ignore($username, 'Username')],
        'nik' => 'nullable|string|max:50',
        'password' => 'nullable|string|min:6|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $dataToUpdate = $request->only(['namapemeriksa', 'email', 'nik']);

    if ($request->filled('password')) {
        $dataToUpdate['Password'] = $request->password;
    }

    DB::connection('sqlsrv')->table('SATUSEHATLOGIN')
        ->where('Username', $username)
        ->update($dataToUpdate);

    // 🔹 Update session langsung
    $updatedUser = array_merge($user, $dataToUpdate);
    $request->session()->put('user', $updatedUser);

    // 🔹 Return JSON agar bisa diproses di JavaScript
    return response()->json([
        'status' => 'success',
        'message' => 'Profil berhasil diperbarui!',
        'user' => $updatedUser,
    ]);
}

    
}