<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use PDO;
use Barryvdh\DomPDF\Facade\Pdf;
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

                // Secara eksplisit tambahkan NoPendaftaran dan NoKwitansi ke patientInfo
                $patientInfo['NoPendaftaran'] = $noPendaftaran;
                $patientInfo['NoKwitansi'] = $noKwitansi;

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

    public function exportAllLabPdf(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');

        if (!$noPendaftaran) {
            return response('No Pendaftaran tidak valid.', 400);
        }

        try {
            $conn = $this->getDbConnection();

            // Logika ini sama dengan showLabResults untuk mengumpulkan semua data
            $queryKwitansi = "SELECT DISTINCT NoKwitansi FROM RincianKwitansi WHERE NoPendaftaran = ? AND kdunit = '105'";
            $stmtKwitansi = $conn->prepare($queryKwitansi);
            $stmtKwitansi->execute([$noPendaftaran]);
            $noKwitansiList = $stmtKwitansi->fetchAll(PDO::FETCH_COLUMN, 0);

            $allKwitansiData = [];

            foreach ($noKwitansiList as $noKwitansi) {
                $sqlLab = "{CALL PrintPemeriksaanLab(?, ?)}";
                $stmtLab = $conn->prepare($sqlLab);
                $stmtLab->execute([$noPendaftaran, $noKwitansi]);
                $labResults = $stmtLab->fetchAll(PDO::FETCH_ASSOC);
                $stmtLab->closeCursor();

                if (empty($labResults)) continue;

                $sqlHiv = "{CALL HasilB20New(?, ?)}";
                $stmtHiv = $conn->prepare($sqlHiv);
                $stmtHiv->execute([$noPendaftaran, $noKwitansi]);
                $hivResults = $stmtHiv->fetchAll(PDO::FETCH_ASSOC);
                $stmtHiv->closeCursor();

                $groupedResults = [];
                foreach ($labResults as $result) {
                    $groupedResults[$result['GroupPemeriksaan'] ?? 'Lainnya'][] = $result;
                }

                $patientInfo = $labResults[0];
                $patientInfo['Usia'] = $this->calculateAge($patientInfo['TanggalLahir'] ?? null);

                $allKwitansiData[] = compact('patientInfo', 'groupedResults', 'hivResults');
            }

            $firstPatientInfo = !empty($allKwitansiData) ? $allKwitansiData[0]['patientInfo'] : null;
            $pdf = Pdf::loadView('rme.igd.forms.penunjang.lab.pdf', ['allKwitansiData' => $allKwitansiData, 'patientInfo' => $firstPatientInfo]);

            $fileName = 'Hasil Lab - ' . ($firstPatientInfo['Namapasien'] ?? 'Pasien') . ' - ' . ($firstPatientInfo['NoReg'] ?? '000000') . '.pdf';

            return $pdf->stream($fileName);
        } catch (\Exception $e) {
            Log::error("Error generating all lab PDF for NOPENDAFTARAN: $noPendaftaran. Error: " . $e->getMessage());
            return response('Gagal membuat PDF laboratorium gabungan. Error: ' . htmlspecialchars($e->getMessage()), 500);
        }
    }

    public function exportLabPdf(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        $noKwitansi = $request->input('NoKwitansi');

        if (!$noPendaftaran || !$noKwitansi) {
            return response('No Pendaftaran atau No Kwitansi tidak valid.', 400);
        }

        try {
            $conn = $this->getDbConnection();

            // 1. Get lab results for the specific kwitansi
            $sqlLab = "{CALL PrintPemeriksaanLab(?, ?)}";
            $stmtLab = $conn->prepare($sqlLab);
            $stmtLab->execute([$noPendaftaran, $noKwitansi]);
            $labResults = $stmtLab->fetchAll(PDO::FETCH_ASSOC);
            $stmtLab->closeCursor();

            if (empty($labResults)) {
                return response('Tidak ada data lab untuk kwitansi ini.', 404);
            }

            // 2. Get HIV results for the specific kwitansi
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

            // Data untuk PDF harus dalam format array yang sama dengan exportAllLabPdf
            // yaitu array of kwitansi data.
            $data = [
                'allKwitansiData' => [
                    compact('patientInfo', 'groupedResults', 'hivResults')
                ],
                'patientInfo' => $patientInfo // Pass patientInfo separately for the title
            ];

            $pdf = Pdf::loadView('rme.igd.forms.penunjang.lab.pdf', $data); 

            // Nama file: LAB-NoRM-NamaPasien-Tgl.pdf
            $fileName = 'Hasil Lab - ' . ($patientInfo['Namapasien'] ?? 'Pasien') . ' - ' . ($patientInfo['NoReg'] ?? '000000') . '.pdf';
            return $pdf->stream($fileName);

        } catch (\Exception $e) {
            Log::error("Error generating lab PDF for NOPENDAFTARAN: $noPendaftaran, NOKWITANSI: $noKwitansi. Error: " . $e->getMessage());
            return response('Gagal membuat PDF laboratorium. Error: ' . htmlspecialchars($e->getMessage()), 500);
        }
    }

    // Anda bisa menambahkan fungsi showRadResults di sini nanti
    public function showRadResults(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');
        // $noPendaftaran = 'SPPP.20251122.056';

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

        // Normalize path separators to forward slashes for consistent checking
        $normalizedImagePath = strtoupper(str_replace('\\', '/', $imagePath));
        $allowedPathNetwork = strtoupper(str_replace('\\', '/', "\\\\192.168.30.6\\Fotorontgen\\"));
        $allowedPathLocal = strtoupper(str_replace('\\', '/', "E:\\Fotorontgen\\"));

        // Security check: ensure the path is within the allowed shared folder
        // The check is now more robust against different path formats.
        if (strpos($normalizedImagePath, $allowedPathNetwork) !== 0 && strpos($normalizedImagePath, $allowedPathLocal) !== 0) {
            Log::warning("Forbidden access attempt for image path: {$imagePath}.");
            Log::warning("Normalized path: {$normalizedImagePath}. Allowed paths: {$allowedPathNetwork}, {$allowedPathLocal}");
            abort(403, 'Access to this image path is forbidden.');
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

    public function showObatResults(Request $request)
    {
        $noPendaftaran = $request->input('NoPendaftaran');

        if (!$noPendaftaran) {
            return response('<div class="alert alert-danger">No Pendaftaran tidak valid.</div>', 400);
        }

        try {
            $conn = $this->getDbConnection();

            // Get Obat (Medicine) results
            $sqlObat = "{CALL RincianBonApotikNew(?)}";
            $stmtObat = $conn->prepare($sqlObat);
            $stmtObat->execute([$noPendaftaran]);
            $obatResults = $stmtObat->fetchAll(PDO::FETCH_ASSOC);
            $stmtObat->closeCursor();

            // Get BHP (Medical Supplies) results
            $sqlBhp = "{CALL RincianBonApotikBhp(?)}";
            $stmtBhp = $conn->prepare($sqlBhp);
            $stmtBhp->execute([$noPendaftaran]);
            $bhpResults = $stmtBhp->fetchAll(PDO::FETCH_ASSOC);
            $stmtBhp->closeCursor();

            // Group Obat results by NomorPenjualan
            $groupedObat = [];
            foreach ($obatResults as $item) {
                $nomorPenjualan = $item['NomorPenjualan'];
                if (!isset($groupedObat[$nomorPenjualan])) {
                    $groupedObat[$nomorPenjualan] = [
                        'Tanggal' => $item['TglObat'],
                        'Dokter' => $item['NamaDokter'],
                        'Items' => [],
                        'Subtotal' => $item['RpObat']
                    ];
                }
                $groupedObat[$nomorPenjualan]['Items'][] = $item;
            }

            // Group BHP results by NomorPenjualan
            $groupedBhp = [];
            foreach ($bhpResults as $item) {
                $nomorPenjualan = $item['NomorPenjualan'];
                if (!isset($groupedBhp[$nomorPenjualan])) {
                    $groupedBhp[$nomorPenjualan] = [
                        'Tanggal' => $item['TglObat'],
                        'Dokter' => $item['NamaDokter'],
                        'Items' => [],
                        'Subtotal' => $item['RpObat']
                    ];
                }
                $groupedBhp[$nomorPenjualan]['Items'][] = $item;
            }

            return view('rme.igd.forms.penunjang.obat.index', compact('obatResults', 'bhpResults', 'groupedObat', 'groupedBhp'));
        } catch (\Exception $e) {
            Log::error("Error fetching obat results for NOPENDAFTARAN: $noPendaftaran. Error: " . $e->getMessage());
            return response('<div class="alert alert-danger">Gagal memuat data obat. Error: ' . htmlspecialchars($e->getMessage()) . '</div>', 500);
        }
    }
}
