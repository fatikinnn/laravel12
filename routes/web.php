<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Rm5Controller;
use App\Http\Middleware\CheckRole;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanKronisController;
use App\Http\Controllers\Rm28Controller;
use App\Http\Controllers\Rm3fController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\EwsController;
use App\Http\Controllers\CpptController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Rm3bController;
use App\Http\Controllers\Rm3aController;
use App\Http\Controllers\Rm48aController;
use App\Http\Controllers\Rm6aController;
use App\Http\Controllers\PenunjangController;
use App\Http\Controllers\Rm26Controller;
use App\Http\Controllers\Rm9a3Controller;
use App\Http\Controllers\Rm6bController;
use App\Http\Controllers\Rm29Controller;
use App\Http\Controllers\Rm6aDewasaController;
use App\Http\Controllers\Rm1cController;
use App\Http\Controllers\Rm3dController;
use App\Http\Controllers\RmeIgdController;
use App\Http\Controllers\McuController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MonitoringLukaReedaController;
use App\Http\Controllers\ResepKronisController;
use App\Http\Controllers\VisiteDokter\VisiteDokterController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    return redirect()->route('login');
});

// Rute Autentikasi
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
// Ubah logout menjadi GET agar bisa diakses langsung dari URL (misal: window.location.href)
// Tambahkan juga POST untuk menjaga kompatibilitas jika ada form logout lain yang masih menggunakan POST.
Route::match(['get', 'post'], 'logout', [LoginController::class, 'logout'])->name('logout');



// Rute yang memerlukan autentikasi
Route::middleware(['auth.custom'])->group(function () {
    // Dashboard bisa diakses oleh semua role yang login
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    // Route untuk pengecekan sesi secara berkala dari client-side
    Route::get('/session/ping', function () {
        return response()->json(['status' => 'ok']);
    })->name('session.ping');

    // Rute untuk RME IGD (dikelompokkan)
    Route::prefix('rme/igd')->name('rme.igd.')->group(function () {
        Route::get('/', [RmeIgdController::class, 'index'])->name('index');
        Route::get('/search-visits', [RmeIgdController::class, 'searchVisits'])->name('searchVisits');
        Route::get('/get-patient-details', [RmeIgdController::class, 'getPatientDetails'])->name('getPatientDetails');
    
        // Grouping untuk semua form RME IGD
        Route::prefix('form')->name('form.')->group(function () {
            // Routes untuk Form RM3A
            Route::get('/rm3a/load', [Rm3aController::class, 'load'])->name('rm3a.load');
            Route::post('/rm3a/store', [Rm3aController::class, 'storeOrUpdate'])->name('rm3a.store');
    
            // Routes untuk Form RM3B
            Route::get('/rm3b/load', [Rm3bController::class, 'load'])->name('rm3b.load');
            Route::post('/rm3b/store', [Rm3bController::class, 'storeOrUpdate'])->name('rm3b.store');

            // Routes untuk Form RM3F (BARU)
            Route::get('/rm3f/load', [Rm3fController::class, 'load'])->name('rm3f.load');
            Route::post('/rm3f/store', [Rm3fController::class, 'storeOrUpdate'])->name('rm3f.store');

            // Routes untuk Form RM28 (BARU)
            Route::get('/rm28/load', [Rm28Controller::class, 'load'])->name('rm28.load');
            Route::post('/rm28/store', [Rm28Controller::class, 'storeOrUpdate'])->name('rm28.store');
            Route::get('/rm28/image/{noPendaftaran}/{field}', [Rm28Controller::class, 'showImage'])->name('rm28.showImage')->where('noPendaftaran', '.*');

            // Routes untuk Form RM1C (BARU)
            Route::get('/rm1c/load', [Rm1cController::class, 'load'])->name('rm1c.load');
            Route::post('/rm1c/store', [Rm1cController::class, 'storeOrUpdate'])->name('rm1c.store');
            
            // Routes untuk Form rm5 (BARU)
            Route::get('/rm5/load', [Rm5Controller::class, 'load'])->name('rm5.load');
            Route::post('/rm5/store', [rm5Controller::class, 'storeOrUpdate'])->name('rm5.store');
            // Routes untuk Form RM57
            Route::get('/rm57/load', [App\Http\Controllers\Rm57Controller::class, 'load'])->name('rm57.load');
            Route::post('/rm57/store', [App\Http\Controllers\Rm57Controller::class, 'store'])->name('rm57.store');

            // Routes untuk Form RM6A (BARU)
            Route::get('/rm6a/load', [Rm6aController::class, 'load'])->name('rm6a.load');
            Route::post('/rm6a/store', [Rm6aController::class, 'storeOrUpdate'])->name('rm6a.store');

            // Routes untuk Form RM6A Dewasa (BARU)
            Route::get('/rm6a_dewasa/load', [Rm6aDewasaController::class, 'load'])->name('rm6a_dewasa.load');
            Route::post('/rm6a_dewasa/store', [Rm6aDewasaController::class, 'storeOrUpdate'])->name('rm6a_dewasa.store');

            // Routes untuk Form RM26 (BARU)
            Route::get('/rm26/load', [Rm26Controller::class, 'load'])->name('rm26.load');
            Route::post('/rm26/store', [Rm26Controller::class, 'storeOrUpdate'])->name('rm26.store');

            // Routes untuk Form RM6B (BARU)
            Route::get('/rm6b/load', [Rm6bController::class, 'load'])->name('rm6b.load');
            Route::get('/rm6b/history', [Rm6bController::class, 'getHistory'])->name('rm6b.history');
            Route::get('/rm6b/detail', [Rm6bController::class, 'getDetail'])->name('rm6b.detail');
            Route::post('/rm6b/store', [Rm6bController::class, 'store'])->name('rm6b.store');

            // Routes untuk Form RM4A (BARU)
            Route::get('/rm4a/load', [App\Http\Controllers\Rm4aController::class, 'load'])->name('rm4a.load');
            Route::get('/rm4a/history', [App\Http\Controllers\Rm4aController::class, 'getHistory'])->name('rm4a.history');
            Route::get('/rm4a/detail', [App\Http\Controllers\Rm4aController::class, 'getDetail'])->name('rm4a.detail');
            Route::post('/rm4a/store', [App\Http\Controllers\Rm4aController::class, 'store'])->name('rm4a.store');

           // Contoh penambahan route di web.php
           Route::prefix('rm25a')->name('rm25a.')->group(function () {
               Route::get('/load', [App\Http\Controllers\Rm25aController::class, 'load'])->name('load');
               Route::get('/history', [App\Http\Controllers\Rm25aController::class, 'getHistory'])->name('history');
               Route::get('/detail', [App\Http\Controllers\Rm25aController::class, 'getDetail'])->name('detail');
               Route::post('/store', [App\Http\Controllers\Rm25aController::class, 'store'])->name('store');
           }); 

            Route::prefix('rm8a')->name('rm8a.')->group(function () {
                Route::get('/load', [App\Http\Controllers\Rm8aController::class, 'load'])->name('load');
                Route::get('/history', [App\Http\Controllers\Rm8aController::class, 'history'])->name('history');
                Route::get('/detail', [App\Http\Controllers\Rm8aController::class, 'detail'])->name('detail');
                Route::post('/store', [App\Http\Controllers\Rm8aController::class, 'store'])->name('store');
                Route::post('/destroy', [App\Http\Controllers\Rm8aController::class, 'destroy'])->name('destroy');
                Route::get('/image/{noPendaftaran}/{counter}', [App\Http\Controllers\Rm8aController::class, 'showImage'])->name('showImage')->where('noPendaftaran', '.*');
            });

            // Routes untuk Form RM3D
            Route::get('/rm3d/load', [Rm3dController::class, 'load'])->name('rm3d.load');
            Route::post('/rm3d/store', [Rm3dController::class, 'store'])->name('rm3d.store');
            // Routes untuk Form RM9A3 (Partograf)
            Route::prefix('rm9a3')->name('rm9a3.')->group(function () {
                Route::get('/load', [Rm9a3Controller::class, 'load'])->name('load');
                Route::post('/store', [Rm9a3Controller::class, 'store'])->name('store');
            });

            // Routes untuk Form RM48A
            Route::prefix('rm48a')->name('rm48a.')->group(function () {
                Route::get('/load', [Rm48aController::class, 'load'])->name('load');
                Route::post('/store', [Rm48aController::class, 'store'])->name('store');
                Route::get('/history', [Rm48aController::class, 'history'])->name('history');
                Route::get('/detail', [Rm48aController::class, 'detail'])->name('detail');
                Route::get('/image/{noPendaftaran}/{counter}/{field}', [Rm48aController::class, 'showImage'])->name('showImage')->where('noPendaftaran', '.*');
            });


            // Routes untuk Form RM29 (BARU)
            Route::get('/rm29/load', [Rm29Controller::class, 'load'])->name('rm29.load');
            Route::post('/rm29/store', [Rm29Controller::class, 'storeOrUpdate'])->name('rm29.store');
            // Routes untuk Form CPPT
            Route::get('/cppt/load', [CpptController::class, 'load'])->name('cppt.load');
            Route::post('/cppt/store', [CpptController::class, 'store'])->name('cppt.store');
            Route::get('/cppt/history', [CpptController::class, 'getHistory'])->name('cppt.history');
            Route::get('/cppt/detail', [CpptController::class, 'getDetail'])->name('cppt.detail');
            Route::get('/cppt/check', [CpptController::class, 'checkData'])->name('cppt.check');

            // Routes untuk Form RM60
            Route::prefix('rm60')->name('rm60.')->group(function () {
                Route::get('/load', [App\Http\Controllers\Rm60Controller::class, 'load'])->name('load');
                Route::get('/history', [App\Http\Controllers\Rm60Controller::class, 'history'])->name('history');
                Route::post('/store', [App\Http\Controllers\Rm60Controller::class, 'store'])->name('store');
                Route::post('/destroy', [App\Http\Controllers\Rm60Controller::class, 'destroy'])->name('destroy');
            });

            // Routes untuk Form RM7
            Route::prefix('rm7')->name('rm7.')->group(function () {
                Route::get('/load', [App\Http\Controllers\Rm7Controller::class, 'load'])->name('load');
                Route::get('/history', [App\Http\Controllers\Rm7Controller::class, 'history'])->name('history');
                Route::get('/detail', [App\Http\Controllers\Rm7Controller::class, 'detail'])->name('detail');
                Route::post('/store', [App\Http\Controllers\Rm7Controller::class, 'store'])->name('store');
                Route::post('/destroy', [App\Http\Controllers\Rm7Controller::class, 'destroy'])->name('destroy');
            });

            Route::prefix('moews')->name('moews.')->group(function () {
                Route::get('/load', [App\Http\Controllers\MoewsController::class, 'load'])->name('load');
                Route::get('/history', [App\Http\Controllers\MoewsController::class, 'history'])->name('history');
                Route::post('/store', [App\Http\Controllers\MoewsController::class, 'store'])->name('store');
                Route::post('/destroy', [App\Http\Controllers\MoewsController::class, 'destroy'])->name('destroy');
            });

            // NEWS
            Route::prefix('news')->name('news.')->group(function () {
                Route::get('/load', [NewsController::class, 'load'])->name('load');
                Route::get('/history', [NewsController::class, 'history'])->name('history');
                Route::post('/store', [NewsController::class, 'store'])->name('store');
                Route::post('/destroy', [NewsController::class, 'destroy'])->name('destroy');
            });

                        // EWS Routes
            Route::prefix('ews')->name('ews.')->group(function () {
                Route::get('/load', [EwsController::class, 'load'])->name('load');
                Route::get('/history', [EwsController::class, 'history'])->name('history');
                Route::post('/store', [EwsController::class, 'store'])->name('store');
                Route::post('/destroy', [EwsController::class, 'destroy'])->name('destroy');
            });


            // PEWS
            Route::prefix('pews')->name('pews.')->group(function () {
                Route::get('/load', [App\Http\Controllers\PewsController::class, 'load'])->name('load');
                Route::get('/history', [App\Http\Controllers\PewsController::class, 'history'])->name('history');
                Route::post('/store', [App\Http\Controllers\PewsController::class, 'store'])->name('store');
                Route::post('/destroy', [App\Http\Controllers\PewsController::class, 'destroy'])->name('destroy');
            });

            // Foto Penunjang Routes
            Route::prefix('fotopenunjang')->name('fotopenunjang.')->group(function () {
                Route::get('/load', [App\Http\Controllers\UploadPenunjangController::class, 'load'])->name('load');
                Route::get('/list', [App\Http\Controllers\UploadPenunjangController::class, 'list'])->name('list');
                Route::get('/show', [App\Http\Controllers\UploadPenunjangController::class, 'show'])->name('show');
                Route::post('/upload', [App\Http\Controllers\UploadPenunjangController::class, 'upload'])->name('upload');
                Route::post('/destroy', [App\Http\Controllers\UploadPenunjangController::class, 'destroy'])->name('destroy');
            });

            // PSI Routes
            Route::prefix('psi')->name('psi.')->group(function () {
                Route::get('/load', [App\Http\Controllers\PsiController::class, 'load'])->name('load');
                Route::post('/store', [App\Http\Controllers\PsiController::class, 'store'])->name('store');
            });

            // PKN Routes
            Route::prefix('pkn')->name('pkn.')->group(function () {
                Route::get('/load', [App\Http\Controllers\PknController::class, 'load'])->name('load');
                Route::post('/store', [App\Http\Controllers\PknController::class, 'store'])->name('store');
            });

            // Skrining Gizi Ibu Hamil Routes
            Route::prefix('skrininggiziibuhamil')->name('skrininggiziibuhamil.')->group(function () {
                Route::get('/load', [App\Http\Controllers\SkriningGiziHamilController::class, 'load'])->name('load');
                Route::post('/store', [App\Http\Controllers\SkriningGiziHamilController::class, 'store'])->name('store');
            });

            // Routes untuk Form RM55
            Route::prefix('rm55')->name('rm55.')->group(function () {
                Route::get('/load', [App\Http\Controllers\Rm55Controller::class, 'load'])->name('load');
                Route::get('/detail', [App\Http\Controllers\Rm55Controller::class, 'detail'])->name('detail');
                Route::post('/store', [App\Http\Controllers\Rm55Controller::class, 'store'])->name('store');
                Route::get('/image/{noPendaftaran}', [App\Http\Controllers\Rm55Controller::class, 'showImage'])->name('showImage')->where('noPendaftaran', '.*');
            });

            // Routes untuk Form RM80 (Berita Acara Serah Terima Bayi)
            Route::prefix('rm80')->name('rm80.')->group(function () {
                Route::get('/load', [App\Http\Controllers\Rm80Controller::class, 'load'])->name('load');
                Route::post('/store', [App\Http\Controllers\Rm80Controller::class, 'store'])->name('store');
                Route::get('/image/{noPendaftaran}/{field}', [App\Http\Controllers\Rm80Controller::class, 'showImage'])->name('showImage')->where('noPendaftaran', '.*');
            });

            // Routes untuk Form Permintaan Lab
            Route::prefix('permintaan-penunjang')->name('permintaan-penunjang.')->group(function () {
                Route::get('/load', [App\Http\Controllers\PermintaanPenunjangController::class, 'load'])->name('load');
                Route::post('/store', [App\Http\Controllers\PermintaanPenunjangController::class, 'store'])->name('store');
                Route::get('/get-lab-services', [App\Http\Controllers\PermintaanPenunjangController::class, 'getLabServices'])->name('getLabServices');
            });

            // Routes untuk Form Penilaian Luka REEDA
            Route::prefix('lukareeda')->name('lukareeda.')->group(function () {
                Route::get('/load', [App\Http\Controllers\LukaReedaController::class, 'load'])->name('load');
                Route::post('/store', [App\Http\Controllers\LukaReedaController::class, 'store'])->name('store');
            });

            // Routes untuk Form Skrining Sepsis
            Route::prefix('skriningsepsis')->name('skriningsepsis.')->group(function () {
                Route::get('/load', [App\Http\Controllers\SkriningSepsisController::class, 'load'])->name('load');
                Route::post('/store', [App\Http\Controllers\SkriningSepsisController::class, 'store'])->name('store');
            });

            // Routes untuk Form MCU
            Route::prefix('mcu')->name('mcu.')->group(function () {
                Route::get('/load', [McuController::class, 'load'])->name('load');
                Route::post('/store', [McuController::class, 'store'])->name('store');
            });

        });

        // Routes for RM16B - Monitoring Infus
        Route::prefix('rm16b')->name('rm16b.')->group(function () {
            Route::get('/load', [App\Http\Controllers\Rm16bController::class, 'load'])->name('load');
            Route::get('/history', [App\Http\Controllers\Rm16bController::class, 'history'])->name('history');
            Route::post('/store', [App\Http\Controllers\Rm16bController::class, 'store'])->name('store');
            Route::post('/destroy', [App\Http\Controllers\Rm16bController::class, 'destroy'])->name('destroy');
        });

        // Routes untuk Form RM24D
        Route::prefix('rm24d')->name('rm24d.')->group(function () {
            Route::get('/load', [App\Http\Controllers\Rm24dController::class, 'load'])->name('load');
            Route::post('/store', [App\Http\Controllers\Rm24dController::class, 'store'])->name('store');
            Route::get('/history', [App\Http\Controllers\Rm24dController::class, 'history'])->name('history');
            Route::post('/destroy', [App\Http\Controllers\Rm24dController::class, 'destroy'])->name('destroy'); // Menggunakan POST untuk delete
            Route::get('/signature/{noPendaftaran}/{counter}', [App\Http\Controllers\Rm24dController::class, 'showSignature'])->name('showSignature')->where('noPendaftaran', '.*');
        });

        // Routes for RM17 - TTV
        Route::prefix('rm17')->name('rm17.')->group(function () {
            Route::get('/load', [App\Http\Controllers\Rm17Controller::class, 'load'])->name('load');
            Route::get('/perawatan-history', [App\Http\Controllers\Rm17Controller::class, 'getPerawatanHistory'])->name('perawatanHistory');
            Route::get('/vitalsign-history', [App\Http\Controllers\Rm17Controller::class, 'getVitalSignHistory'])->name('vitalSignHistory');
            Route::post('/store', [App\Http\Controllers\Rm17Controller::class, 'store'])->name('store');
        });

        // Routes for RM18 - Catatan Pemberian Obat
        Route::prefix('rm18')->name('rm18.')->group(function () {
            Route::get('/load', [App\Http\Controllers\Rm18Controller::class, 'load'])->name('load');
            Route::get('/obat-list', [App\Http\Controllers\Rm18Controller::class, 'getObatList'])->name('obatList');
            Route::get('/history', [App\Http\Controllers\Rm18Controller::class, 'getHistory'])->name('history');
            Route::post('/store', [App\Http\Controllers\Rm18Controller::class, 'store'])->name('store');
            Route::post('/destroy', [App\Http\Controllers\Rm18Controller::class, 'destroy'])->name('destroy');
            Route::get('/signature/{noPendaftaran}/{counter}', [App\Http\Controllers\Rm18Controller::class, 'showSignature'])->name('showSignature')->where('noPendaftaran', '.*');
        });

        // Penunjang (Lab & Rad) Routes - Dipindahkan ke sini
        Route::prefix('penunjang')->name('penunjang.')->group(function () {
            Route::get('/lab', [PenunjangController::class, 'showLabResults'])->name('lab');
            Route::get('/lab/pdf', [PenunjangController::class, 'exportLabPdf'])->name('lab.pdf');
            Route::get('/lab/pdf/all', [PenunjangController::class, 'exportAllLabPdf'])->name('lab.pdf.all');
            Route::get('/rad', [PenunjangController::class, 'showRadResults'])->name('rad');
            Route::get('/obat', [PenunjangController::class, 'showObatResults'])->name('obat');
            Route::get('/rad/image', [PenunjangController::class, 'showRadImage'])->name('rad.image');
        });

    });

    // Routes untuk Form RM24D (dipindahkan ke sini agar konsisten)
    // Rute HANYA untuk ADMIN
    Route::middleware('role:admin')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::post('/users/bulk-store', [UserController::class, 'bulkStore'])->name('users.bulkStore');
        Route::put('/users/{UserID}', [UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{UserID}', [UserController::class, 'destroy'])->name('users.destroy');
    });

    // Rute untuk ADMIN dan FARMASI
    Route::middleware('role:admin,farmasi')->group(function () {
        Route::get('/laporan-kronis', [LaporanKronisController::class, 'index'])->name('laporan-kronis.index');
        Route::post('/laporan-kronis', [LaporanKronisController::class, 'store'])->name('laporan-kronis.store');

        Route::get('/resep-kronis', [ResepKronisController::class, 'index'])->name('resep-kronis.index');
        Route::patch('/resep-kronis/{resepKronis}', [ResepKronisController::class, 'update'])->name('resep-kronis.update');
        Route::post('/resep-kronis/destroy-selected', [ResepKronisController::class, 'destroySelected'])->name('resep-kronis.destroy-selected');
        Route::post('/resep-kronis/clear', [ResepKronisController::class, 'clear'])->name('resep-kronis.clear');
        Route::get('/resep-kronis/export', [ResepKronisController::class, 'export'])->name('resep-kronis.export');
    });
    
    // Rute untuk Visite Dokter, dibatasi untuk role tertentu
    Route::middleware('role:DOKTER,DOKTER UMUM,PERAWAT,PELAYANAN,admin,BIDAN')->group(function () {
        Route::prefix('visite-dokter')->name('visite-dokter.')->group(function () {
            Route::get('/', [VisiteDokterController::class, 'index'])->name('index');
            Route::get('/search-patients', [VisiteDokterController::class, 'searchPatients'])->name('searchPatients');
            Route::get('/get-patient-details', [VisiteDokterController::class, 'getPatientDetails'])->name('getPatientDetails');
        });
    });

    // Rute untuk Monitoring
    Route::prefix('monitoring')->name('monitoring.')->group(function () {
        Route::get('/luka-reeda', [MonitoringLukaReedaController::class, 'index'])->name('lukareeda.index');
        
        // Rute untuk Monitoring MCU (hanya admin)
        Route::middleware('role:admin')->group(function () {
            Route::get('/mcu', [McuController::class, 'monitoringIndex'])->name('mcu.index');
            Route::get('/mcu/data', [McuController::class, 'data'])->name('mcu.data');
        });
    });

});