<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Rm9a3Controller extends Controller
{
    /**
     * Load the main view for RM9A3.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');
 
        // Ambil semua data monitoring yang ada untuk pasien ini
        $rows = DB::connection('sqlsrv')
            ->table('RM9A3')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->orderBy('COUNTER', 'asc')
            ->get()->map(fn ($item) => (array)$item)->all();
 
        // Ambil data statis dari baris pertama jika ada
        $static_data = !empty($rows) ? $rows[0] : [];
 
        // Fetch DPJP/Dokter/Penolong list (sudah ada)
        $pemeriksaList = DB::connection('sqlsrv')
            ->table('PEMERIKSA')
            ->where('ACTIVE', 1)
            ->orderBy('KDJABATAN')
            ->orderBy('NAMAPEMERIKSA')
            ->get(['NOPEMERIKSA', 'NAMAPEMERIKSA']);

        // Fetch Bidan list (sudah ada)
        $bidanList = DB::connection('sqlsrv')
            ->table('RUSER')
            ->whereIn('KODEGROUP', [9, 10, 11, 19, 20, 21, 22, 28, 29, 30, 31, 34, 38, 40, 41, 42, 43, 44, 45, 46, 47, 48])
            ->orderBy('NAMAUSER')
            ->get(['NOUSER', 'NAMAUSER']);

        return view('rme.igd.forms.rm9a3.index', compact('noPendaftaran', 'norm', 'user', 'pemeriksaList', 'bidanList', 'rows', 'static_data'));
    }
 
    /**
     * Store or update RM9A3 data.
     */
    public function store(Request $request)
    {
        try {
            // 1. Ambil semua COUNTER yang sudah ada di database untuk NOPENDAFTARAN ini
            $existingCountersInDb = DB::connection('sqlsrv')
                ->table('RM9A3')
                ->where('NOPENDAFTARAN', $request->input('NOPENDAFTARAN'))
                ->pluck('COUNTER')
                ->toArray();

            // Data statis yang akan diterapkan ke semua baris
            $noPendaftaran = $request->input('NOPENDAFTARAN');
            $userEntry = $request->input('USER_ENTRY');
 
            // Prepare static data that will be applied to all rows
            $staticData = [
                'USER_ENTRY' => $userEntry,
                'TGL_ENTRY' => Carbon::now()->format('Y-m-d H:i:s'),
                'PT_UMUR' => $request->input('PT_UMUR'), 'PT_G' => $request->input('PT_G'), 'PT_P' => $request->input('PT_P'), 'PT_A' => $request->input('PT_A'),
                'PT_DIAGNOSA' => $request->input('PT_DIAGNOSA'), 'PT_HIDUP' => $request->input('PT_HIDUP'), 'PT_HAMIL' => $request->input('PT_HAMIL'),
                'PT_TANGGAL' => $request->input('PT_TANGGAL') ? Carbon::parse($request->input('PT_TANGGAL'))->format('Y-m-d H:i:s') : null,
                'PT_NODPJP' => $request->input('PT_NODPJP') ?: 0, 'PT_NMDPJP' => $this->getNamaPemeriksa($request->input('PT_NODPJP')),
                'PS_TANGGAL' => $request->input('PS_TANGGAL') ? Carbon::parse($request->input('PS_TANGGAL'))->format('Y-m-d H:i:s') : null,
                'PS_JAM' => $request->input('PS_JAM'), 'PS_TINDAKAN' => $request->input('PS_TINDAKAN'), 'PS_PLAC' => $request->input('PS_PLAC'),
                'PS_BAYI' => $request->input('PS_BAYI'), 'PS_INDIKASI' => $request->input('PS_INDIKASI'), 'PS_COTIL' => $request->input('PS_COTIL'),
                'PS_BB' => $request->input('PS_BB'), 'PS_LAMA' => $request->input('PS_LAMA'), 'PS_PDRHN' => $request->input('PS_PDRHN'),
                'PS_PB' => $request->input('PS_PB'), 'PS_PNRM' => $request->input('PS_PNRM'), 'PS_AS' => $request->input('PS_AS'), 'PS_LK' => $request->input('PS_LK'),
                'PS_NODOKTER' => $request->input('PS_NODOKTER') ?: 0, 'PS_NMDOKTER' => $this->getNamaPemeriksa($request->input('PS_NODOKTER')),
                'PS_NOBIDAN' => $request->input('PS_NOBIDAN') ?: 0, 'PS_NMBIDAN' => $this->getNamaBidan($request->input('PS_NOBIDAN')),
                'PS_NOPENOLONG' => $request->input('PS_NOPENOLONG') ?: 0, 'PS_NMPENOLONG' => $this->getNamaPemeriksa($request->input('PS_NOPENOLONG')),
            ];
 
            $partografData = $request->input('partograf', []);
            $submittedCounters = []; // Untuk melacak COUNTER yang benar-benar dikirimkan dan tidak kosong
 
            // If no dynamic rows are submitted, create a dummy one to save the static data.
            // This ensures that static data is always saved to COUNTER 1 if no dynamic rows are present.
            if (empty($partografData)) {
                $partografData[] = [
                    'COUNTER' => 1,
                    // Semua field dinamis diatur ke null agar dianggap kosong
                    'PT_JAM' => null, 'PT_VITAL' => null, 'PT_BUKA' => null, 'PT_KK' => null,
                    'PT_DJJ' => null, 'PT_PORTIO' => null, 'PT_TERABA' => null, 'PT_TURUN' => null,
                    'PT_X' => null, 'PT_DTK' => null, 'PT_KUAT' => null, 'PT_KET' => null, 'PT_PETUGAS' => null,
                ];
            }

            // Definisi field dinamis untuk mengecek apakah baris kosong
            $dynamicFields = [
                'PT_JAM', 'PT_VITAL', 'PT_BUKA', 'PT_KK', 'PT_DJJ', 'PT_PORTIO',
                'PT_TERABA', 'PT_TURUN', 'PT_X', 'PT_DTK', 'PT_KUAT', 'PT_KET', 'PT_PETUGAS'
            ];
 
            foreach ($partografData as $item) {
                $counter = intval($item['COUNTER'] ?? 0);
                if ($counter <= 0) continue; // Pastikan COUNTER valid
 
                // Cek apakah baris dinamis ini kosong (semua field dinamisnya null/kosong)
                $isDynamicRowEmpty = true;
                foreach ($dynamicFields as $field) {
                    if (!empty(trim($item[$field] ?? ''))) { $isDynamicRowEmpty = false; break; }
                }
 
                // Jika baris kosong DAN bukan COUNTER 1 (karena COUNTER 1 bisa menyimpan data statis saja),
                // maka lewati pemrosesan baris ini. Baris ini akan dihapus jika sudah ada di DB.
                if ($isDynamicRowEmpty && $counter > 1) {
                    continue;
                }
 
                // Jika baris tidak kosong, atau ini adalah COUNTER 1, tambahkan ke daftar submittedCounters
                $submittedCounters[] = $counter;

                // Prepare dynamic data for this row
                $dynamicData = [
                    'PT_JAM' => $item['PT_JAM'] ?: null, 'PT_VITAL' => $item['PT_VITAL'] ?: null, 'PT_BUKA' => $item['PT_BUKA'] ?: null,
                    'PT_KK' => $item['PT_KK'] ?: null, 'PT_DJJ' => $item['PT_DJJ'] ?: null, 'PT_PORTIO' => $item['PT_PORTIO'] ?: null,
                    'PT_TERABA' => $item['PT_TERABA'] ?: null, 'PT_TURUN' => $item['PT_TURUN'] ?: null, 'PT_X' => $item['PT_X'] ?: null,
                    'PT_DTK' => $item['PT_DTK'] ?: null, 'PT_KUAT' => $item['PT_KUAT'] ?: null, 'PT_KET' => $item['PT_KET'] ?: null,
                    'PT_PETUGAS' => $item['PT_PETUGAS'] ?: null,
                ];
 
                // Combine static and dynamic data
                $dataToSave = array_merge($staticData, $dynamicData);
 
                // Check if record exists
                $exists = in_array($counter, $existingCountersInDb); // Cek lebih efisien
                    DB::connection('sqlsrv')->table('RM9A3')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->where('COUNTER', $counter)
                    ->exists();
 
                if ($exists) {
                    // Update existing record
                    DB::connection('sqlsrv')->table('RM9A3')
                        ->where('NOPENDAFTARAN', $noPendaftaran)
                        ->where('COUNTER', $counter)
                        ->update($dataToSave);
                } else {
                    // Insert new record
                    $dataToSave['NOPENDAFTARAN'] = $noPendaftaran;
                    $dataToSave['COUNTER'] = $counter;
                    DB::connection('sqlsrv')->table('RM9A3')->insert($dataToSave);
                }
            }
 
            // 3. Identifikasi COUNTER yang harus dihapus
            $countersToDelete = array_diff($existingCountersInDb, $submittedCounters);

            // 4. Lakukan penghapusan untuk COUNTER yang tidak lagi ada di form
            if (!empty($countersToDelete)) {
                DB::connection('sqlsrv')->table('RM9A3')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->whereIn('COUNTER', $countersToDelete)
                    ->delete();
            }
            return response()->json(['status' => 'success', 'message' => 'Data Partograf berhasil disimpan.']);
        } catch (\Exception $e) {
            Log::error('Error storing RM9A3 data: ' . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    private function getNamaPemeriksa($noPemeriksa)
    {
        if (empty($noPemeriksa) || $noPemeriksa == 0) return '';
        $pemeriksa = DB::connection('sqlsrv')
            ->table('PEMERIKSA')
            ->where('NOPEMERIKSA', $noPemeriksa)
            ->first();
        return $pemeriksa ? trim($pemeriksa->NAMAPEMERIKSA) : '';
    }

    private function getNamaBidan($noUser)
    {
        if (empty($noUser) || $noUser == 0) return '';
        $bidan = DB::connection('sqlsrv')
            ->table('RUSER')
            ->where('NOUSER', $noUser)
            ->first();
        return $bidan ? trim($bidan->NAMAUSER) : '';
    }
}