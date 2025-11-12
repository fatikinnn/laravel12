<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Rm26Controller extends Controller
{
    /**
     * Memuat data untuk formulir RM26 dan menampilkan view.
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

        // Fetch RM26 data
        $rm26 = DB::connection('sqlsrv')
            ->table('RM26')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Fetch RM3B data for DIAGNOSIS_UTAMA
        $rm3b = DB::connection('sqlsrv')
            ->table('RM3B')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // If no data exists, create empty objects to avoid errors in the view
        if (!$rm3a) $rm3a = (object)[];
        if (!$rm26) $rm26 = (object)[];
        if (!$rm3b) $rm3b = (object)[];

        return view('rme.igd.forms.rm26.index', compact('noPendaftaran', 'norm', 'user', 'rm3a', 'rm26', 'rm3b'));
    }

    /**
     * Menyimpan atau memperbarui data formulir RM26.
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
            DB::connection('sqlsrv')->table('RM26')->updateOrInsert(
                ['NOPENDAFTARAN' => $noPendaftaran],
                $data
            );
            return response()->json(['status' => 'success', 'message' => 'Data Skrining Gizi Anak berhasil disimpan/diperbarui.']);
        } catch (\Exception $e) {
            Log::error('Error saving RM26: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data. ' . $e->getMessage()], 500);
        }
    }
}