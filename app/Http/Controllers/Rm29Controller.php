<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Rm29Controller extends Controller
{
    /**
     * Memuat data untuk formulir RM29 dan menampilkan view.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        // Fetch RM3A data for BB, TB
        $rm3a = DB::connection('sqlsrv')
            ->table('RM3A')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Fetch RM29 data
        $rm29 = DB::connection('sqlsrv')
            ->table('RM29')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Fetch RM3B data for DIAGNOSIS_UTAMA
        $rm3b = DB::connection('sqlsrv')
            ->table('RM3B')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // If no data exists, create empty objects to avoid errors in the view
        if (!$rm3a) $rm3a = (object)[];
        if (!$rm29) $rm29 = (object)[];
        if (!$rm3b) $rm3b = (object)[];

        return view('rme.igd.forms.rm29.index', compact('noPendaftaran', 'norm', 'user', 'rm3a', 'rm29', 'rm3b'));
    }

    /**
     * Menyimpan atau memperbarui data formulir RM29.
     */
    public function storeOrUpdate(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $data = $request->except(['_token', 'NORM']);

        // Format ulang TGL_SKRINING dari 'Y-m-d\TH:i' ke 'Y-m-d H:i:s'
        if (isset($data['TGL_SKRINING'])) {
            $data['TGL_SKRINING'] = \Carbon\Carbon::parse($data['TGL_SKRINING'])->format('Y-m-d H:i:s');
        }

        try {
            DB::connection('sqlsrv')->table('RM29')->updateOrInsert(
                ['NOPENDAFTARAN' => $noPendaftaran],
                $data
            );
            return response()->json(['status' => 'success', 'message' => 'Data Skrining Gizi Neonatus berhasil disimpan/diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error saving RM29: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data. ' . $e->getMessage()], 500);
        }
    }
}