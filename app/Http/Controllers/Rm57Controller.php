<?php

namespace App\Http\Controllers;
 
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
 
class Rm57Controller extends Controller // Pastikan ini meng-extend Controller dasar Laravel
{
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');
        $patientDetails = $request->all();

        try { 
            // Get existing data if any
            $query = "SELECT DIP.*, RM57.*
                      FROM DIP
                      LEFT JOIN RM57 ON DIP.NOPENDAFTARAN = RM57.NOPENDAFTARAN
                      WHERE DIP.NOPENDAFTARAN = ?";
            $data = DB::connection('sqlsrv')->selectOne($query, [$noPendaftaran]);
 
            // Get doctor list
            $dokterList = DB::connection('sqlsrv')
                ->table('PEMERIKSA')
                ->select('NAMAPEMERIKSA')
                ->where('ACTIVE', '1')
                ->where('NAMAPEMERIKSA', 'like', '%dr.%')
                ->orderBy('NAMAPEMERIKSA')
                ->get(); 

            return view('rme.igd.forms.rm57.index', [
                'noPendaftaran' => $noPendaftaran,
                'norm' => $norm,
                'user' => $user,
                'patientDetails' => $patientDetails,
                'data' => $data,
                'dokterList' => $dokterList,
            ]);
        } catch (\Exception $e) { 
            Log::error('Error loading RM57 form: ' . $e->getMessage());
            return response()->view('errors.custom', ['message' => 'Gagal memuat formulir RM57.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $noPendaftaran = $request->input('NOPENDAFTARAN'); 
            $user = session('user')['username'] ?? 'SYSTEM';

            $dataToSave = [
                'USER_ENTRY' => $user,
                'TGLJAM_ENTRY' => Carbon::now(),
                'DOKTERBIDAN' => $request->input('DOKTERBIDAN'),
                'NM_ISTRI' => $request->input('NM_ISTRI'),
                'NM_SUAMI' => $request->input('NM_SUAMI'),
                'ALAMAT' => $request->input('ALAMAT'),
                'HARI' => $request->input('HARI'),
                'TANGGAL' => $request->input('TANGGAL'),
                'JAM' => $request->input('JAM'),
                'NM_BAYI' => $request->input('NM_BAYI'),
                'JENIS_KELAMIN' => $request->input('JENIS_KELAMIN'),
                'BB' => $request->input('BB'),
                'KETERANGAN' => $request->input('KETERANGAN'),
            ]; 

            // Check if record exists
            $existingRecord = DB::connection('sqlsrv')
                ->table('RM57')
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->first(); 

            if ($existingRecord) {
                // Update existing record
                DB::connection('sqlsrv')
                    ->table('RM57')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->update($dataToSave);
                $message = 'Data berhasil diperbarui.';
            } else {
                // Insert new record
                $dataToSave['NOPENDAFTARAN'] = $noPendaftaran;
                DB::connection('sqlsrv')
                    ->table('RM57')
                    ->insert($dataToSave);
                $message = 'Data berhasil disimpan.';
            }
 
            return response()->json(['status' => 'success', 'message' => $message]);

        } catch (\Exception $e) {
            Log::error('Error storing RM57 data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan saat menyimpan data.'], 500);
        }
    }
}