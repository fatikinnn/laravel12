@extends('layouts.app')

@section('title', 'Dashboard')

@section('content-header')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Dashboard</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Selamat Datang!</h3>
            </div>
            <div class="card-body">
                <p>Halo, <strong>{{ session('user')['namapemeriksa'] ?? session('user')['username'] }}</strong>! Selamat datang di aplikasi.</p>
                <p>Silakan gunakan menu navigasi di samping untuk mengakses fitur yang Anda butuhkan.</p>
            </div>
        </div>
    </div>
</div>
@endsection