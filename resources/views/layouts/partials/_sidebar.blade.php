<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ route('dashboard') }}" class="brand-link">
        <img src="{{ asset('img/logo.png') }}" alt="Logo" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">RSUI Mutiara Bunda</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image text-center">
                <i class="fas fa-user-circle fa-2x text-white"></i>
            </div>
            <div class="info">
                <a href="#" class="d-block">{{ session('user.namapemeriksa') ?? session('user.username') }}</a>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
                     with font-awesome or any other icon font library -->
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>
                            Dashboard
                        </p>
                    </a>
                </li>

                @php
                    // Daftar role yang bisa mengakses menu RME
                    $rmeAccessRoles = ['DOKTER', 'DOKTER UMUM', 'PERAWAT', 'PELAYANAN', 'admin', 'BIDAN'];
                @endphp

                @if(in_array(session('user.access'), $rmeAccessRoles))
                    <li class="nav-item">
                        <a href="{{ route('rme.igd.index') }}"
                            class="nav-link {{ request()->routeIs('rme.igd.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-procedures"></i>
                            <p>RME IGD</p>
                        </a>
                    </li>
                @endif

                @if(session('user.access') == 'admin')
                    <li class="nav-header">MENU UTAMA</li>

                    {{-- Menu hanya untuk Admin --}}
                    <li class="nav-item">
                        <a href="{{ route('users.index') }}"
                            class="nav-link {{ request()->routeIs('users.index') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-users-cog"></i>
                            <p>
                                Manajemen Pengguna
                            </p>
                        </a>
                    </li>

                @endif


                {{-- Menu untuk Admin dan Farmasi --}}
                @if(in_array(session('user.access'), ['admin', 'farmasi']))
                    <li
                        class="nav-item {{ request()->routeIs('laporan-kronis.*') || request()->routeIs('resep-kronis.*') ? 'menu-open' : '' }}">
                        <a href="#"
                            class="nav-link {{ request()->routeIs('laporan-kronis.*') || request()->routeIs('resep-kronis.*') ? 'active' : '' }}">
                            <i class="nav-icon fas fa-prescription-bottle-alt"></i>
                            <p>
                                Resep Obat Kronis
                                <i class="right fas fa-angle-left"></i>
                            </p>
                        </a>

                        <ul class="nav nav-treeview">
                            <li class="nav-item">
                                <a href="{{ route('resep-kronis.index') }}"
                                    class="nav-link {{ request()->routeIs('resep-kronis.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Lihat Data Resep</p>
                                </a>
                            </li>

                            <li class="nav-item">
                                <a href="{{ route('laporan-kronis.index') }}"
                                    class="nav-link {{ request()->routeIs('laporan-kronis.*') ? 'active' : '' }}">
                                    <i class="far fa-circle nav-icon"></i>
                                    <p>Filter Obat</p>
                                </a>
                            </li>
                        </ul>
                    </li>

                @endif
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>