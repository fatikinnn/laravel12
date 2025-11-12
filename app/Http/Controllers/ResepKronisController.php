<?php

namespace App\Http\Controllers;

use App\Models\ResepKronis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResepKronisController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = ResepKronis::query();

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('tanggal_daftar', [$startDate, $endDate . ' 23:59:59']);
        } elseif (!empty($startDate)) {
            $query->where('tanggal_daftar', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('tanggal_daftar', '<=', $endDate . ' 23:59:59');
        }

        $reseps = $query->orderBy('tanggal_daftar')
            ->orderBy('noresep')
            ->orderBy('id')
            ->get();

        // Get all IDs for select all functionality
        $allIdsQuery = ResepKronis::query();
        
        if (!empty($startDate) && !empty($endDate)) {
            $allIdsQuery->whereBetween('tanggal_daftar', [$startDate, $endDate . ' 23:59:59']);
        } elseif (!empty($startDate)) {
            $allIdsQuery->where('tanggal_daftar', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $allIdsQuery->where('tanggal_daftar', '<=', $endDate . ' 23:59:59');
        }

        $allIds = $allIdsQuery->pluck('id')->toArray();

        return view('resep-kronis.index', compact('reseps', 'startDate', 'endDate', 'allIds'));
    }

    public function update(Request $request, ResepKronis $resepKronis)
    {
        $request->validate([
            'noresep' => 'required|integer|min:1',
        ]);

        $newNoresep = $request->input('noresep');
        $no_sep = $request->input('no_sep');
        $norm = $request->input('norm');
        $tanggal_daftar = $request->input('tanggal_daftar');

        try {
            DB::transaction(function () use ($resepKronis, $newNoresep, $no_sep, $norm, $tanggal_daftar) {
                // Update specific record first
                $resepKronis->update(['noresep' => $newNoresep]);

                // Then update all related records with the same no_sep, norm, and tanggal_daftar
                ResepKronis::where('no_sep', $no_sep)
                    ->where('norm', $norm)
                    ->where('tanggal_daftar', $tanggal_daftar)
                    ->where('id', '!=', $resepKronis->id)
                    ->update(['noresep' => $newNoresep]);
            });
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal memperbarui data: ' . $e->getMessage()], 500);
        }

        return response()->json(['status' => 'success', 'message' => 'Data berhasil diperbarui', 'new_noresep' => $newNoresep]);
    }

    public function destroySelected(Request $request)
    {
        $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'integer',
        ]);

        $ids = $request->input('ids');

        try {
            DB::transaction(function () use ($ids) {
                // 1. Ambil informasi data yang akan dihapus (sebelum dihapus)
                $resepsToDelete = ResepKronis::whereIn('id', $ids)->get(['id', 'noresep', 'norm', 'tanggal_daftar']);

                // 2. Kelompokkan berdasarkan kunci unik (norm + tanggal_daftar)
                $deletedGroups = [];
                foreach ($resepsToDelete as $resep) {
                    $groupKey = $resep->norm . '_' . $resep->tanggal_daftar;
                    if (!isset($deletedGroups[$groupKey])) {
                        $deletedGroups[$groupKey] = [
                            'noresep' => $resep->noresep,
                            'norm' => $resep->norm,
                            'tanggal_daftar' => $resep->tanggal_daftar
                        ];
                    }
                }

                // 3. Hapus data yang dipilih
                $deletedCount = ResepKronis::whereIn('id', $ids)->delete();

                if ($deletedCount > 0) {
                    // 4. Proses penyesuaian nomor resep
                    foreach ($deletedGroups as $group) {
                        $currentNoresep = $group['noresep'];
                        $norm = $group['norm'];
                        $tanggal_daftar = $group['tanggal_daftar'];

                        // Cek apakah masih ada data dengan noresep, norm, dan tanggal_daftar yang sama
                        $remainingCount = ResepKronis::where('noresep', $currentNoresep)
                            ->where('norm', $norm)
                            ->where('tanggal_daftar', $tanggal_daftar)
                            ->count();
                        
                        // Jika tidak ada lagi, update noresep yang lebih besar pada tanggal yang sama
                        if ($remainingCount === 0) {
                            ResepKronis::where('tanggal_daftar', $tanggal_daftar)
                                ->where('noresep', '>', $currentNoresep)
                                ->orderBy('noresep', 'asc')
                                ->decrement('noresep');
                        }
                    }
                }
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil dihapus dan nomor resep diperbarui.',
                'deleted_count' => count($ids)
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    public function clear()
    {
        try {
            ResepKronis::truncate();
            return response()->json(['status' => 'success', 'message' => 'Semua data berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Gagal menghapus data: ' . $e->getMessage()], 500);
        }
    }

    public function export(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = ResepKronis::query();

        if (!empty($startDate) && !empty($endDate)) {
            $query->whereBetween('tanggal_daftar', [$startDate, $endDate . ' 23:59:59']);
        } elseif (!empty($startDate)) {
            $query->where('tanggal_daftar', '>=', $startDate);
        } elseif (!empty($endDate)) {
            $query->where('tanggal_daftar', '<=', $endDate . ' 23:59:59');
        }

        $reseps = $query->orderBy('tanggal_daftar')
            ->orderBy('noresep')
            ->orderBy('id')
            ->get();

        // Date info for export
        $dateInfo = "";
        if (!empty($startDate) && !empty($endDate)) {
            $dateInfo = "Data dari tanggal " . date('d/m/Y', strtotime($startDate)) . " sampai " . date('d/m/Y', strtotime($endDate));
        } elseif (!empty($startDate)) {
            $dateInfo = "Data dari tanggal " . date('d/m/Y', strtotime($startDate));
        } elseif (!empty($endDate)) {
            $dateInfo = "Data sampai tanggal " . date('d/m/Y', strtotime($endDate));
        } else {
            $dateInfo = "Data seluruh periode";
        }

        return view('resep-kronis.export', compact('reseps', 'startDate', 'endDate', 'dateInfo'));
    }
}