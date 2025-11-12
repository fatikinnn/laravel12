<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Rm6aDewasaController extends Controller
{
    /**
     * Memuat data untuk formulir RM6A Dewasa dan menampilkan view.
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

        // Fetch RM6A Dewasa data
        $rm6a_dewasa = DB::connection('sqlsrv')
            ->table('RM6A_DEWASA')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // Fetch RM3B data for DIAGNOSIS_UTAMA
        $rm3b = DB::connection('sqlsrv')
            ->table('RM3B')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        // If no data exists, create empty objects to avoid errors in the view
        if (!$rm3a) $rm3a = (object)[];
        if (!$rm6a_dewasa) $rm6a_dewasa = (object)[];
        if (!$rm3b) $rm3b = (object)[];

        return view('rme.igd.forms.rm6a_dewasa.index', compact('noPendaftaran', 'norm', 'user', 'rm3a', 'rm6a_dewasa', 'rm3b'));
    }

    /**
     * Menyimpan atau memperbarui data formulir RM6A Dewasa.
     */
    public function storeOrUpdate(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        
        $data = $request->except(['_token', 'NORM']);

        // 1. Format ulang TGL_SKRINING dari 'Y-m-d\TH:i' ke 'Y-m-d H:i:s'
        if (isset($data['TGL_SKRINING'])) {
            $data['TGL_SKRINING'] = \Carbon\Carbon::parse($data['TGL_SKRINING'])->format('Y-m-d H:i:s');
        }

        // 2. Pastikan nilai PENURUNAN_BB_YA adalah null jika kosong, bukan string kosong.
        if (isset($data['PENURUNAN_BB_YA']) && $data['PENURUNAN_BB_YA'] === '') {
            $data['PENURUNAN_BB_YA'] = null;
        }

        try {
            DB::connection('sqlsrv')->table('RM6A_DEWASA')->updateOrInsert(
                ['NOPENDAFTARAN' => $noPendaftaran],
                $data
            );
            return response()->json(['status' => 'success', 'message' => 'Data Skrining Gizi Dewasa berhasil disimpan.']);
        } catch (\Exception $e) {
            Log::error('Error saving RM6A Dewasa: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data. ' . $e->getMessage()], 500);
        }
    }
}