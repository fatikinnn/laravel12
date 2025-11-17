<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class SkriningGiziHamilController extends Controller
{
    /**
     * Load the form view with existing data.
     */
    public function load(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NoPendaftaran' => 'required|string',
            'NoRM' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response('Parameter tidak valid.', 400);
        }

        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');

        $data = DB::connection('sqlsrv')
            ->table('SKRININGGIZIHAMIL')
            ->where('NOPENDAFTARAN', $noPendaftaran)
            ->first();

        return view('rme.igd.forms.skrininggiziibuhamil.index', [
            'noPendaftaran' => $noPendaftaran,
            'norm' => $norm,
            'data' => $data ? (array) $data : [],
        ]);
    }

    /**
     * Store or update the form data.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'NOPENDAFTARAN' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'No Pendaftaran wajib diisi.'], 422);
        }

        $noPendaftaran = $request->input('NOPENDAFTARAN');
        $user = Auth::user();
        $username = $user ? $user->username : 'system';
        $now = Carbon::now();

        $data = [
            'USER_ENTRY' => $username,
            'TGLJAM_ENTRY' => $now,
            'TGL_SKRINING' => $request->input('TGL_SKRINING') ? Carbon::parse($request->input('TGL_SKRINING')) : $now,
            'NAFSUMAKAN_YA' => $request->input('NAFSUMAKAN') == '1' ? 1 : 0,
            'NAFSUMAKAN_TIDAK' => $request->input('NAFSUMAKAN') == '0' ? 1 : 0,
            'METABOLISME_YA' => $request->input('METABOLISME') == '1' ? 1 : 0,
            'METABOLISME_TIDAK' => $request->input('METABOLISME') == '0' ? 1 : 0,
            'GANGGUANLAIN' => $request->input('GANGGUANLAIN'),
            'BERTAMBAHBB_YA' => $request->input('BERTAMBAHBB') == '1' ? 1 : 0,
            'BERTAMBAHBB_TIDAK' => $request->input('BERTAMBAHBB') == '0' ? 1 : 0,
            'NILAIHB_YA' => $request->input('NILAIHB') == '1' ? 1 : 0,
            'NILAIHB_TIDAK' => $request->input('NILAIHB') == '0' ? 1 : 0,
            'TOTALSKOR' => $request->input('TOTALSKOR'),
            'DIETFISIEN_KET' => $request->input('DIETFISIEN_KET'),
        ];

        try {
            DB::connection('sqlsrv')->transaction(function () use ($noPendaftaran, $data) {
                $existing = DB::connection('sqlsrv')
                    ->table('SKRININGGIZIHAMIL')
                    ->where('NOPENDAFTARAN', $noPendaftaran)
                    ->first();

                if ($existing) {
                    DB::connection('sqlsrv')
                        ->table('SKRININGGIZIHAMIL')
                        ->where('NOPENDAFTARAN', $noPendaftaran)
                        ->update($data);
                } else {
                    $data['NOPENDAFTARAN'] = $noPendaftaran;
                    DB::connection('sqlsrv')
                        ->table('SKRININGGIZIHAMIL')
                        ->insert($data);
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Data Skrining Gizi Ibu Hamil berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menyimpan data: ' . $e->getMessage()], 500);
        }
    }
}