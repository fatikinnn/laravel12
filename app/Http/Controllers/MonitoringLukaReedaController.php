<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MonitoringLukaReedaController extends Controller
{
    /**
     * Menampilkan halaman monitoring Penilaian Luka REEDA.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\View|\Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = DB::connection('sqlsrv')
                ->table('LUKAREEDA as lr')
                ->join('DIP as dip', 'lr.NOPENDAFTARAN', '=', 'dip.NOPENDAFTARAN')
                ->select(
                    'lr.NOPENDAFTARAN',
                    'dip.NORM',
                    'dip.NAMAPASIEN',
                    'lr.TOTALSKOR',
                    'lr.INTERPRETASI',
                    'lr.TGLJAM_ENTRY',
                    'lr.USER_ENTRY'
                )
                ->orderBy('TGLJAM_ENTRY', 'desc')
                ->get();

            // Kembalikan sebagai JSON standar untuk client-side processing
            return response()->json(['data' => $query]);
        }

        return view('monitoring-lukareeda.index');
    }
}