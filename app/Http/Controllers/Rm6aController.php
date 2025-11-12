<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Rm6aController extends Controller
{
    /**
     * Memuat data untuk formulir RM6A dan menampilkan view.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        // Fetch RM3A data for BB, TB, IMT
        $rm3a = DB::connection('sqlsrv')
            ->table('RM3A')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Fetch RM6A data
        $rm6a = DB::connection('sqlsrv')
            ->table('RM6A')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Fetch RM3B data for DIAGNOSIS_UTAMA
        $rm3b = DB::connection('sqlsrv')
            ->table('RM3B')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // If no data exists, create empty objects to avoid errors in the view
        if (!$rm3a) $rm3a = (object)[]; // RM3A might be needed for TB/BB even if RM6A is new
        if (!$rm6a) $rm6a = (object)[];
        if (!$rm3b) $rm3b = (object)[];

        return view('rme.igd.forms.rm6a.index', compact('noPendaftaran', 'norm', 'user', 'rm3a', 'rm6a', 'rm3b'));
    }

    /**
     * Menyimpan atau memperbarui data formulir RM6A.
     */
    public function storeOrUpdate(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $data = $request->except('_token', 'NORM'); // NORM tidak ada di tabel RM6A, jadi kita kecualikan.

        try {
            DB::connection('sqlsrv')->table('RM6A')->updateOrInsert(
                ['NOPENDAFTARAN' => $noPendaftaran],
                $data
            );
            return response()->json(['status' => 'success', 'message' => 'Data RM6A berhasil disimpan/diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error saving RM6A: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data RM6A. ' . $e->getMessage()], 500);
        }
    }
}