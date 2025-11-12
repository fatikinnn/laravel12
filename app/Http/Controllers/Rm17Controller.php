<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Rm17Controller extends Controller
{
    /**
     * Memuat view utama untuk form RM17.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');
        $patientDetails = $request->all();

        return view('rme.igd.forms.rm17.index', compact('noPendaftaran', 'norm', 'user', 'patientDetails'));
    }

    /**
     * Mengambil riwayat data perawatan dari CPPTPERAWAT.
     */
    public function getPerawatanHistory(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');
            $data = DB::connection('sqlsrv')
                ->table('CPPTPERAWAT')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('TGLUPDATED', 'desc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error loading CPPTPERAWAT history for RM17: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat riwayat perawatan.'], 500);
        }
    }

    /**
     * Mengambil riwayat tanda vital untuk grafik.
     */
    public function getVitalSignHistory(Request $request)
    {
        try {
            $noPendaftaran = $request->input('noPendaftaran');
            $data = DB::connection('sqlsrv')
                ->table('RM17')
                ->select('TANGGAL', 'JAM', 'NADI', 'SUHU')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('TANGGAL', 'asc')
                ->orderBy('NOJAM', 'asc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $data]);
        } catch (\Exception $e) {
            Log::error('Error loading RM17 chart data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat data grafik.'], 500);
        }
    }

    /**
     * Menyimpan data tanda vital baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nopendaftaran' => 'required',
            'tanggal' => 'required|date',
            'jam' => 'required',
        ]);

        try {
            $noPendaftaran = $request->input('nopendaftaran');
            $tanggal = Carbon::parse($request->input('tanggal'))->format('Y-m-d');
            $jam = $request->input('jam');
            
            list($hours, $minutes) = explode(':', $jam);
            $nojam = (int)$hours * 100 + (int)$minutes;

            $nonadi = $request->input('nonadi', 0);
            $nadi = $request->input('nadi');
            $nosuhu = $request->input('nosuhu', 0);
            $suhu = $request->input('suhu');
            $nafas = $request->input('nafas');
            $td = $request->input('td');
            $userentri = session('user.username', 'SYSTEM');

            $sql = "EXEC SaveRM17SP_fatikin ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?";
            
            DB::connection('sqlsrv')->statement($sql, [
                $noPendaftaran,
                $tanggal,
                $nojam,
                $jam,
                $nonadi,
                $nadi,
                $nosuhu,
                $suhu,
                $nafas,
                $td,
                $userentri
            ]);

            return response()->json(['status' => 'success', 'message' => 'Data Tanda Vital berhasil disimpan.']);

        } catch (\Exception $e) {
            Log::error('Error saving RM17 data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}