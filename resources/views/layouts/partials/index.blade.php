@extends('layouts.app')

@section('title', 'RME IGD')

@push('styles')
    <style>
        /* Style untuk layout 2 kolom di halaman RME */
        .rme-layout {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .rme-sidebar-left {
            flex: 1;
            min-width: 320px;
            max-width: 100%;
        }

        .rme-content-right {
            flex: 2;
            min-width: 320px;
            max-width: 100%;
        }

        @media (min-width: 992px) {
            .rme-sidebar-left {
                flex: 0 0 350px; /* Lebar tetap untuk sidebar kiri di layar besar */
            }
            .rme-content-right {
                flex: 1;
            }
        }
    </style>
@endpush

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Rekam Medis Elektronik IGD</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">RME IGD</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<div class="rme-layout">
    {{-- KOLOM KIRI - PENCARIAN PASIEN --}}
    <div class="rme-sidebar-left">
        <div class="card card-primary card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-search mr-1"></i>
                    Pencarian Pasien
                </h3>
            </div>
            <div class="card-body">
                <form id="search-patient-form">
                    <div class="form-group">
                        <label for="patient_search">No. RM / Nama Pasien</label>
                        <input type="text" id="patient_search" class="form-control" placeholder="Ketik untuk mencari...">
                    </div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search mr-2"></i>Cari Pasien
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- KOLOM KANAN - HASIL PENCARIAN & KUNJUNGAN --}}
    <div class="rme-content-right">
        <div class="card card-success card-outline shadow-sm">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-history mr-1"></i>
                    Riwayat Kunjungan Pasien
                </h3>
            </div>
            <div class="card-body">
                <p class="text-muted text-center">Silakan cari pasien terlebih dahulu untuk menampilkan riwayat kunjungannya.</p>
                {{-- Tabel untuk menampilkan hasil kunjungan akan ditempatkan di sini --}}
                {{-- Contoh tabel (saat ini disembunyikan) --}}
                <div class="table-responsive" style="display: none;" id="visit-history-table">
                    {{-- Tabel akan diisi melalui JavaScript/AJAX setelah pencarian berhasil --}}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
{{-- Script untuk handle pencarian pasien via AJAX akan ditambahkan di sini nanti --}}
@endpush