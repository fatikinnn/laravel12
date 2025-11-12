<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class Rm3dController extends Controller
{
    /**
     * Memuat data untuk form RM3D.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        // Ambil data dari masing-masing tabel
        $rm3d1 = (array) DB::connection('sqlsrv')->table('RM3D1')->where('NOPENDAFTARAN', $noPendaftaran)->first();
        $rm3d2 = (array) DB::connection('sqlsrv')->table('RM3D2')->where('NOPENDAFTARAN', $noPendaftaran)->first();
        $rm3d3 = (array) DB::connection('sqlsrv')->table('RM3D3')->where('NOPENDAFTARAN', $noPendaftaran)->first();
        $rm16a1detil = DB::connection('sqlsrv')->table('RM16A1DETIL')->where('NOPENDAFTARAN', $noPendaftaran)->orderBy('COUNTER', 'asc')->get()->map(fn ($item) => (array)$item)->all();

        return view('rme.igd.forms.rm3d.index', compact('noPendaftaran', 'norm', 'user', 'rm3d1', 'rm3d2', 'rm3d3', 'rm16a1detil'));
    }

    /**
     * Menyimpan atau memperbarui data form RM3D.
     */
    public function store(Request $request)
    {
        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $norm = $request->input('NORM');
        $userEntry = $request->input('USER_ENTRY');
        $tglJamEntry = Carbon::now()->format('Y-m-d H:i:s');

        try {
            DB::connection('sqlsrv')->transaction(function () use ($request, $noPendaftaran, $norm, $userEntry, $tglJamEntry) {
                // 1. Proses data untuk tabel RM3D1
                $dataRm3d1 = $this->prepareData($request->all(), 'rm3d1_');
                $dataRm3d1['USER_ENTRY'] = $userEntry;
                $dataRm3d1['TGLJAM_ENTRY'] = $tglJamEntry;
                // Format tanggal dengan benar sebelum disimpan
                if (isset($dataRm3d1['TGLJAMDATANG'])) {
                    $dataRm3d1['TGLJAMDATANG'] = Carbon::parse($dataRm3d1['TGLJAMDATANG'])->format('Y-m-d H:i:s');
                }
                DB::connection('sqlsrv')->table('RM3D1')->updateOrInsert(
                    ['NOPENDAFTARAN' => $noPendaftaran],
                    $dataRm3d1
                );

                // 2. Proses data untuk tabel RM3D2
                $dataRm3d2 = $this->prepareData($request->all(), 'rm3d2_');
                $dataRm3d2['USER_ENTRY'] = $userEntry;
                $dataRm3d2['TGLJAM_ENTRY'] = $tglJamEntry;
                DB::connection('sqlsrv')->table('RM3D2')->updateOrInsert(
                    ['NOPENDAFTARAN' => $noPendaftaran],
                    $dataRm3d2
                );

                // 3. Proses data untuk tabel RM3D3
                $dataRm3d3 = $this->prepareData($request->all(), 'rm3d3_');
                $dataRm3d3['USER_ENTRY'] = $userEntry;
                $dataRm3d3['TGLJAM_ENTRY'] = $tglJamEntry;
                DB::connection('sqlsrv')->table('RM3D3')->updateOrInsert(
                    ['NOPENDAFTARAN' => $noPendaftaran],
                    $dataRm3d3
                );

                // 4. Proses data untuk tabel RM16A1DETIL (Riwayat Persalinan)
                $rm16a1detilData = $request->input('rm16a1detil', []);
                $submittedCounters = [];

                if (!empty($rm16a1detilData)) {
                    foreach ($rm16a1detilData as $item) {
                        $counter = $item['COUNTER'];
                        $submittedCounters[] = $counter;

                        $dataToSave = [
                            'TAHUN' => isset($item['TAHUN']) ? Carbon::parse($item['TAHUN'])->format('Y-m-d H:i:s') : null,
                            'PARTUS' => $item['PARTUS'] ?? null,
                            'UMUR' => $item['UMUR'] ?? null,
                            'JENIS' => $item['JENIS'] ?? null,
                            'PENOLONG' => $item['PENOLONG'] ?? null,
                            'PENYULIT' => $item['PENYULIT'] ?? null,
                            'NIFAS' => $item['NIFAS'] ?? null,
                            'BBPB' => $item['BBPB'] ?? null,
                            'JK' => $item['JK'] ?? null,
                            'KEADAAN' => $item['KEADAAN'] ?? null,
                        ];

                        DB::connection('sqlsrv')->table('RM16A1DETIL')->updateOrInsert(
                            ['NOPENDAFTARAN' => $noPendaftaran, 'COUNTER' => $counter],
                            $dataToSave
                        );
                    }
                }

                // Hapus baris yang tidak lagi ada di form
                DB::connection('sqlsrv')->table('RM16A1DETIL')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->whereNotIn('COUNTER', $submittedCounters)
                    ->delete();
            });

            return response()->json(['status' => 'success', 'message' => 'Data Asesmen Kebidanan berhasil disimpan.']);

        } catch (\Exception $e) {
            Log::error('Error storing RM3D data: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Helper untuk mempersiapkan data dari request.
     * Mengambil semua input dengan prefix tertentu, menghapus prefix,
     * dan mengubah checkbox yang tidak dicentang menjadi nilai 0.
     */
    private function prepareData(array $inputs, string $prefix): array
    {
        $data = [];
        $prefixLength = strlen($prefix);

        foreach ($inputs as $key => $value) {
            if (strpos($key, $prefix) === 0) {
                $newKey = substr($key, $prefixLength);
                $data[$newKey] = $value;
            }
        }

        // Handle checkbox yang tidak terkirim (tidak dicentang)
        // Ini adalah pendekatan sederhana; untuk form kompleks, Anda mungkin perlu daftar eksplisit.
        // Script di blade sudah mengirim nilai '0' untuk checkbox yang tidak dicentang,
        // jadi blok ini mungkin bersifat redundan tapi aman sebagai jaring pengaman.
        foreach ($this->getCheckboxFieldsForPrefix($prefix) as $checkboxField) {
            if (!isset($data[$checkboxField])) {
                $data[$checkboxField] = 0;
            }
        }

        return $data;
    }

    /**
     * Daftar nama field checkbox untuk setiap prefix.
     * Ini membantu memastikan nilai 0 disimpan jika checkbox tidak dicentang.
     */
    private function getCheckboxFieldsForPrefix(string $prefix): array
    {
        $fields = [
            'rm3d1_' => [
                'CARMAS_IRJ', 'CARMAS_IGD', 'CARMAS_DOKTERPRIBADI', 'CARMAS_PONEK', 'CARMAS_DIANTAROLEH', 'RUJUKAN',
                'RUJUKAN_PUSKES', 'RUJUKAN_BIDAN', 'RUJUKAN_RB', 'RUJUKAN_DOKTER', 'RUJUKAN_RS', 'DIKIRIMPOLISI',
                'SUNTIK', 'PIL', 'AKDR', 'MOW', 'PERDARAHAN', 'PID', 'LAINLAIN', 'DM', 'HPETITIS', 'STROKE', 'GINJAL',
                'HYPERTENSI', 'TBC', 'JANTUNG', 'KEGANASAN', 'RIW_LAINLAIN', 'DIRAWAT_YA', 'DIRAWAT_TDK', 'OPERASI_YA',
                'OPERASI_TIDAK', 'INFERTILITAS', 'INFEKSIVIRUS', 'PMS', 'KRONIS', 'ENDOMETRIOSIS', 'MIOMA', 'POLIP',
                'KANKER', 'PERKOSAAN', 'OPERASIKANDUNGAN', 'JAMU', 'AKUPUNTUR', 'PIJAT', 'TERAPI_LAINLAIN', 'RA_TIDAK',
                'RA_ADA', 'MEROKOK_TIDAK', 'MEROKOK_YA', 'OBATIDUR_TIDAK', 'OBATIDUR_YA', 'OLAHRAGA_TDK', 'OLAHRAGA_YA',
                'ALKOHOL_TDK', 'ALKOHOL_YA', 'UMRMENARRCHE', 'HPHT', 'DISMENORROE', 'SPOOTING', 'MENORHAGIA', 'PREMENS',
                'MSHMENIKAH_1', 'CERAI_1', 'MENINGGAL_1', 'MSHMENIKAH_2', 'CERAI_2', 'MENINGGAL_2', 'MSHMENIKAH_3', 'CERAI_3',
                'BIO_YA', 'BIO_TDK', 'PSIKO_SEDANG', 'PSIKO_BERAT', 'PSIKO_PANIK', 'SPIRIT_YA', 'SPIRIT_TDK', 'NYERI_YA',
                'NYERI_TDK', 'METODE_VAS'
            ],
            // Tambahkan field checkbox untuk rm3d2_ dan rm3d3_ jika diperlukan
            'rm3d2_' => [],
            'rm3d3_' => [],
        ];

        return $fields[$prefix] ?? [];
    }
}

?>