<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use DateTime;

class PenunjangController extends Controller
{
    private function getDbConnection()
    {
        // Menggunakan konfigurasi dari config/database.php
        $connection = DB::connection('sqlsrv')->getPdo();
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $connection;
    }

    private function calculateAge($birthDate)
    {
        if (!$birthDate) return '-';
        try {
            $birthDate = new DateTime($birthDate);
            $today = new DateTime();
            $age = $today->diff($birthDate);

            $result = '';
            if ($age->y > 0) $result .= $age->y . ' tahun ';
            if ($age->m > 0) $result .= $age->m . ' bulan ';
            if ($age->d > 0 && $age->y == 0) $result .= $age->d . ' hari';

            return trim($result) ?: '-';
        } catch (\Exception $e) {
            return '-';
        }
    }

    public function showLabResults(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');

        if (!$noPendaftaran) {
            return response('<div class="alert alert-danger">No Pendaftaran tidak valid.</div>', 400);
        }

        try {
            $conn = $this->getDbConnection();

            // 1. Get all NoKwitansi for the given NoPendaftaran
            $queryKwitansi = "SELECT DISTINCT NoKwitansi FROM RincianKwitansi WHERE NoPendaftaran = ? AND kdunit = '105'";
            $stmtKwitansi = $conn->prepare($queryKwitansi);
            $stmtKwitansi->execute([$noPendaftaran]);
            $noKwitansiList = $stmtKwitansi->fetchAll(PDO::FETCH_COLUMN, 0);

            $allKwitansiData = [];

            foreach ($noKwitansiList as $noKwitansi) {
                // 2. Get lab results for each kwitansi
                $sqlLab = "{CALL PrintPemeriksaanLab(?, ?)}";
                $stmtLab = $conn->prepare($sqlLab);
                $stmtLab->execute([$noPendaftaran, $noKwitansi]);
                $labResults = $stmtLab->fetchAll(PDO::FETCH_ASSOC);
                $stmtLab->closeCursor();

                if (empty($labResults)) continue;

                // 3. Get HIV results for each kwitansi
                $sqlHiv = "{CALL HasilB20New(?, ?)}";
                $stmtHiv = $conn->prepare($sqlHiv);
                $stmtHiv->execute([$noPendaftaran, $noKwitansi]);
                $hivResults = $stmtHiv->fetchAll(PDO::FETCH_ASSOC);
                $stmtHiv->closeCursor();

                // Group lab results by GroupPemeriksaan
                $groupedResults = [];
                foreach ($labResults as $result) {
                    $group = $result['GroupPemeriksaan'] ?? 'Lainnya';
                    $groupedResults[$group][] = $result;
                }

                // Get patient info from the first result
                $patientInfo = $labResults[0];
                $patientInfo['Usia'] = $this->calculateAge($patientInfo['TanggalLahir'] ?? null);

                $allKwitansiData[] = [
                    'patientInfo' => $patientInfo,
                    'groupedResults' => $groupedResults,
                    'hivResults' => $hivResults,
                ];
            }

            return view('rme.igd.forms.penunjang.lab.index', ['allKwitansiData' => $allKwitansiData]);

        } catch (\Exception $e) {
            Log::error("Error fetching lab results for NOPENDAFTARAN: $noPendaftaran. Error: " . $e->getMessage());
            return response('<div class="alert alert-danger">Gagal memuat data laboratorium. Error: ' . htmlspecialchars($e->getMessage()) . '</div>', 500);
        }
    }

    // Anda bisa menambahkan fungsi showRadResults di sini nanti
    public function showRadResults(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');

        if (!$noPendaftaran) {
            return response('<div class="alert alert-danger">No Pendaftaran tidak valid.</div>', 400);
        }

        try {
            $conn = $this->getDbConnection();

            // 1. Get all NoKwitansi for radiologi unit (106)
            $queryKwitansi = "SELECT DISTINCT NoKwitansi FROM RincianKwitansi WHERE NoPendaftaran = ? AND kdunit = '106'";
            $stmtKwitansi = $conn->prepare($queryKwitansi);
            $stmtKwitansi->execute([$noPendaftaran]);
            $noKwitansiList = $stmtKwitansi->fetchAll(PDO::FETCH_COLUMN, 0);

            $allKwitansiData = [];

            foreach ($noKwitansiList as $noKwitansi) {
                // 2. Get radiologi results
                $sqlRad = "{CALL PrintPemRadiologiNew(?, ?)}";
                $stmtRad = $conn->prepare($sqlRad);
                $stmtRad->execute([$noPendaftaran, $noKwitansi]);
                $radiologiResults = $stmtRad->fetchAll(PDO::FETCH_ASSOC);
                $stmtRad->closeCursor();

                if (empty($radiologiResults)) continue;

                // 3. Get image path
                $imageQuery = "SELECT DIR_FOTO FROM pemradiologi WHERE NOPENDAFTARAN = ? AND NOKWI = ?";
                $imageStmt = $conn->prepare($imageQuery);
                $imageStmt->execute([$noPendaftaran, $noKwitansi]);
                $imageData = $imageStmt->fetch(PDO::FETCH_ASSOC);
                $imageStmt->closeCursor();

                $allKwitansiData[] = [
                    'resultData' => $radiologiResults[0],
                    'imagePath' => $imageData['DIR_FOTO'] ?? null,
                ];
            }

            return view('rme.igd.forms.penunjang.rad.index', ['allKwitansiData' => $allKwitansiData]);

        } catch (\Exception $e) {
            Log::error("Error fetching radiology results for NOPENDAFTARAN: $noPendaftaran. Error: " . $e->getMessage());
            return response('<div class="alert alert-danger">Gagal memuat data radiologi. Error: ' . htmlspecialchars($e->getMessage()) . '</div>', 500);
        }
    }

    public function showRadImage(Request $request)
    {
        $imagePath = $request->query('path');
        if (!$imagePath) {
            abort(404, 'Image path not provided.');
        }

        // Security check: ensure the path is within the allowed shared folder
        $allowedPath = "\\\\192.168.30.6\\Fotorontgen\\";
        if (strpos($imagePath, $allowedPath) !== 0) {
            abort(403, 'Access to this path is forbidden.');
        }

        if (!file_exists($imagePath)) {
            abort(404, 'Image not found.');
        }

        try {
            $file = file_get_contents($imagePath);
            $type = mime_content_type($imagePath);
            return response($file)->header("Content-Type", $type);
        } catch (\Exception $e) {
            Log::error("Could not read image file: {$imagePath}. Error: " . $e->getMessage());
            abort(500, 'Could not read image file.');
        }
    }
}
