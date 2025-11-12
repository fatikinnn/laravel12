<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class UploadPenunjangController extends Controller
{
    /**
     * Load the main view for photo uploads.
     */
    public function load(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $norm = $request->input('NoRM');
        $user = session('user');

        return view('rme.igd.forms.FotoPenunjang.index', compact('noPendaftaran', 'norm', 'user'));
    }

    /**
     * Get the mapping of photo type to table and folder names.
     */
    private function getTypeMapping($type)
    {
        $map = [
            'usg' => ['table' => 'HASILUSG', 'folder' => 'FotoUSG'],
            'ekg' => ['table' => 'HASILEKG', 'folder' => 'FotoEKG'],
            'spirometri' => ['table' => 'HASILSPIROMETRI', 'folder' => 'FotoSpirometri'],
            'ctg' => ['table' => 'HASILCTG', 'folder' => 'FotoCTG'],
            'echo' => ['table' => 'HASILECHO', 'folder' => 'FotoECHO'],
            'penunjangluar' => ['table' => 'HASILPENUNJANGLUAR', 'folder' => 'FotoPenunjangLuar'],
        ];
        return $map[$type] ?? null;
    }

    /**
     * List existing photos for a given type.
     */
    public function list(Request $request)
    {
        $noPendaftaran = $request->input('nopendaftaran');
        $type = $request->input('type');
        $mapping = $this->getTypeMapping($type);

        if (!$mapping) {
            return response()->json(['status' => 'error', 'message' => 'Jenis foto tidak valid.'], 400);
        }

        try {
            $photos = DB::connection('sqlsrv')
                ->table($mapping['table'])
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->orderBy('COUNTER', 'asc')
                ->get();

            return response()->json(['status' => 'success', 'data' => $photos]);
        } catch (\Exception $e) {
            Log::error("Error fetching photos for type {$type}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal memuat data foto.'], 500);
        }
    }

    /**
     * Handle photo uploads.
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nopendaftaran' => 'required|string',
            'norm' => 'required|string',
            'type' => 'required|string|in:usg,ekg,spirometri,ctg,echo,penunjangluar',
            'photos' => 'required|array',
            'photos.*' => 'required|image|mimes:jpeg,jpg|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'message' => 'Validasi gagal.', 'errors' => $validator->errors()], 422);
        }

        $mapping = $this->getTypeMapping($request->type);
        if (!$mapping) {
            return response()->json(['status' => 'error', 'message' => 'Jenis foto tidak valid.'], 400);
        }

        $noPendaftaran = $request->nopendaftaran;
        $norm = $request->norm;
        $user = data_get(session('user'), 'username', 'system');
        $nopenFile = str_replace('.', '', $noPendaftaran);
        $serverPath = "\\\\192.168.30.10\\" . $mapping['folder'] . "\\";

        $uploadedFiles = [];
        $errors = [];

        DB::connection('sqlsrv')->beginTransaction();
        try {
            $maxCounterResult = DB::connection('sqlsrv')->table($mapping['table'])->where('NOPENDAFTARAN', $noPendaftaran)->max('COUNTER');
            $counter = ($maxCounterResult ?? 0) + 1;

            foreach ($request->file('photos') as $file) {
                $fileName = "{$nopenFile}_{$norm}_" . Carbon::now()->format('dmY_His') . "_{$counter}.jpg";
                $destination = $serverPath . $fileName;

                if (File::put($destination, $file->get())) {
                    DB::connection('sqlsrv')->table($mapping['table'])->insert([
                        'NOPENDAFTARAN' => $noPendaftaran,
                        'NORM' => $norm,
                        'USER_ENTRY' => $user,
                        'TGLJAM_ENTRY' => now(),
                        'COUNTER' => $counter,
                        'DIR_FOTO' => "{$mapping['folder']}/{$fileName}",
                    ]);
                    $uploadedFiles[] = $fileName;
                    $counter++;
                } else {
                    $errors[] = "Gagal menyimpan file {$file->getClientOriginalName()}";
                }
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => count($uploadedFiles) . ' foto berhasil diupload.', 'errors' => $errors]);

        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error("Upload error for type {$request->type}: " . $e->getMessage());
            // Clean up successfully moved files if DB fails
            foreach ($uploadedFiles as $fileName) {
                @unlink($serverPath . $fileName);
            }
            return response()->json(['status' => 'error', 'message' => 'Terjadi kesalahan pada server.'], 500);
        }
    }

    /**
     * Delete a photo.
     */
    public function destroy(Request $request)
    {
        $noPendaftaran = $request->input('nopendaftaran');
        $counter = $request->input('counter');
        $type = $request->input('type');
        $mapping = $this->getTypeMapping($type);

        if (!$mapping || !$noPendaftaran || !$counter) {
            return response()->json(['status' => 'error', 'message' => 'Parameter tidak valid.'], 400);
        }

        DB::connection('sqlsrv')->beginTransaction();
        try {
            $photo = DB::connection('sqlsrv')
                ->table($mapping['table'])
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counter)
                ->first();

            if (!$photo) {
                DB::connection('sqlsrv')->rollBack();
                return response()->json(['status' => 'error', 'message' => 'Foto tidak ditemukan.'], 404);
            }

            $deleted = DB::connection('sqlsrv')
                ->table($mapping['table'])
                ->where('NOPENDAFTARAN', $noPendaftaran)
                ->where('COUNTER', $counter)
                ->delete();

            if ($deleted) {
                $fileName = basename($photo->DIR_FOTO);
                $filePath = "\\\\192.168.30.10\\" . $mapping['folder'] . "\\" . $fileName;
                if (File::exists($filePath)) {
                    File::delete($filePath);
                }
            }

            DB::connection('sqlsrv')->commit();
            return response()->json(['status' => 'success', 'message' => 'Foto berhasil dihapus.']);

        } catch (\Exception $e) {
            DB::connection('sqlsrv')->rollBack();
            Log::error("Delete error for type {$type}: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus foto.'], 500);
        }
    }

    /**
     * Serve as a proxy to show images from the network share.
     */
    public function show(Request $request)
    {
        $type = $request->query('type');
        $fileName = $request->query('filename');
        $mapping = $this->getTypeMapping($type);

        if (!$mapping || !$fileName || strpos($fileName, '..') !== false) {
            return response('File tidak ditemukan.', 404);
        }

        $path = "\\\\192.168.30.10\\" . $mapping['folder'] . "\\" . $fileName;

        if (!File::exists($path)) {
            return response('File tidak ditemukan.', 404);
        }

        // Tambahkan pengecekan apakah file bisa dibaca
        if (!File::isReadable($path)) {
            Log::error("File is not readable: {$path}. Check network share permissions for the web server user.");
            return response('File tidak dapat dibaca karena masalah perizinan.', 403); // 403 Forbidden lebih sesuai
        }

        return response()->file($path);
    }
}