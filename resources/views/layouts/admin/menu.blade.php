<nav class="mt-2 d-flex flex-column" style="height: 100vh; position: relative;">
    <ul class="nav nav-pills nav-sidebar flex-column flex-grow-1" data-widget="treeview" role="menu"
        data-accordion="false">

        @role('cleaning_services')
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.cleaning.index') }}" class="nav-link text-white">Cleaning Services</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole

        @role('ekspedisi')
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.ekspedisi.index') }}" class="nav-link text-white">Ekspedisi</a></li>
                    </ul>
                </div>
            </li>
        @endrole

        @role('interior_consultan')
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.interior_consultan.index') }}"
                                class="nav-link text-white">Interior Consultan</a></li>
                    </ul>
                </div>
            </li>
        @endrole

        @role('marketing')
            <li class="nav-item">
                <a href="{{ route('dashboard.marketing') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-chart-line"></i>
                    <p>Dashboard Marketing</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.digital_marketing.list') }}" class="nav-link text-white">Digital
                                Marketing</a></li>
                    </ul>
                </div>
            </li>
        @endrole

        @role('admin')
            <li class="nav-item">
                <a href="{{ route('surat.admin.dashboard') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-user-shield"></i>
                    <p>Dashboard Administrasi</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.admin.index') }}" class="nav-link text-white">Administrasi</a></li>
                        <li><a href="{{ route('surat.interior_consultan.index') }}"
                                class="nav-link text-white">Interior_Consultan</a></li>
                        <li><a href="{{ route('surat.ekspedisi.index') }}" class="nav-link text-white">Ekspedisi</a></li>
                        <li><a href="{{ route('surat.cleaning.index') }}" class="nav-link text-white">Cleaining Services</a>
                        </li>
                    </ul>
                </div>
            </li>
            <li class="nav-item">
                <a href="{{ route('progress_projects.index') }}"
                    class="nav-link text-white {{ Request::routeIs('progress_projects.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tasks"></i>
                    <p>Progress Project</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('maintenances.index') }}"
                    class="nav-link text-white {{ Request::routeIs('maintenances.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cogs"></i>
                    <p>Maintenance</p>
                </a>
            </li>
        @endrole

        @role('finance')
            <li class="nav-item">
                <a href="{{ route('surat.finance.dashboard') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-coins"></i>
                    <p>Dashboard Finance</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.digital_marketing.list') }}" class="nav-link text-white">Digital
                                Marketing</a></li>
                        <li><a href="{{ route('surat.finance.index') }}" class="nav-link text-white">Finance</a></li>
                        <li><a href="{{ route('surat.admin.index') }}" class="nav-link text-white">Administrasi</a></li>
                        <li><a href="{{ route('surat.warehouse.index') }}" class="nav-link text-white">Warehouse</a></li>
                        <li><a href="{{ route('surat.purchasing.index') }}" class="nav-link text-white">Purchasing</a>
                        </li>

                    </ul>
                </div>
            </li>
        @endrole

        @role('warehouse')
            <li class="nav-item">
                <a href="{{ route('surat.warehouse.dashboard') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-warehouse"></i>
                    <p>Dashboard Warehouse</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>
                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.warehouse.index') }}" class="nav-link text-white">Warehouse</a></li>
                    </ul>
                </div>
            </li>
        @endrole

        @role('purchasing')
            <li class="nav-item">
                <a href="{{ route('surat.purchasing.dashboard') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-shopping-cart"></i>
                    <p>Dashboard Purchasing</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>

                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.digital_marketing.list') }}" class="nav-link text-white">Digital
                                Marketing</a></li>
                        <li><a href="{{ route('surat.finance.index') }}" class="nav-link text-white">Finance</a></li>
                        <li><a href="{{ route('surat.admin.index') }}" class="nav-link text-white">Administrasi</a></li>
                        <li><a href="{{ route('surat.warehouse.index') }}" class="nav-link text-white">Warehouse</a></li>
                        <li><a href="{{ route('surat.purchasing.index') }}" class="nav-link text-white">Purchasing</a>
                        </li>
                    </ul>
                </div>
            </li>
        @endrole

        @role('CEO')
            <li class="nav-item">
                <a href="{{ route('dashboard.ceo') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>Dashboard Direktur</p>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('surat.finance.pending') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-user-tie"></i>
                    <p>Surat Masuk</p>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>

                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.digital_marketing.list') }}" class="nav-link text-white">Digital
                                Marketing</a></li>
                        <li><a href="{{ route('surat.finance.index') }}" class="nav-link text-white">Finance</a></li>
                        <li><a href="{{ route('surat.admin.index') }}" class="nav-link text-white">Administrasi</a></li>
                        <li><a href="{{ route('surat.warehouse.index') }}" class="nav-link text-white">Warehouse</a></li>
                        <li><a href="{{ route('surat.purchasing.index') }}" class="nav-link text-white">Purchasing</a>
                        </li>
                        <li><a href="{{ route('surat.interior_consultan.index') }}" class="nav-link text-white">Interior
                                Consultan</a></li>
                        <li><a href="{{ route('surat.ekspedisi.index') }}" class="nav-link text-white">Ekspedisi</a></li>
                        <li><a href="{{ route('surat.cleaning.index') }}" class="nav-link text-white">Cleaning
                                Services</a></li>
                    </ul>
                </div>
            </li>
        @endrole

        @role('superadmin')
            <li class="nav-item">
                <a href="{{ route('users.index') }}" class="nav-link text-white">
                    <i class="nav-icon fas fa-users"></i>
                    <p>User</p>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white d-flex justify-content-start align-items-center" data-bs-toggle="collapse"
                    href="#menuSurat" role="button" aria-expanded="false" aria-controls="menuSurat">
                    <i class="nav-icon fas fa-envelope"></i>
                    <p class="ms-2 mb-0">Surat</p>
                    <i class="fas fa-chevron-down ms-auto"></i>
                </a>

                <div class="collapse" id="menuSurat">
                    <ul class="ps-4 list-unstyled">
                        <li><a href="{{ route('surat.digital_marketing.list') }}" class="nav-link text-white">Digital
                                Marketing</a></li>
                        <li><a href="{{ route('surat.finance.index') }}" class="nav-link text-white">Finance</a></li>
                        <li><a href="{{ route('surat.admin.index') }}" class="nav-link text-white">Administrasi</a></li>
                        <li><a href="{{ route('surat.warehouse.index') }}" class="nav-link text-white">Warehouse</a></li>
                        <li><a href="{{ route('surat.purchasing.index') }}" class="nav-link text-white">Purchasing</a>
                        </li>
                        <li><a href="{{ route('surat.interior_consultan.index') }}"
                                class="nav-link text-white">Interior Consultan</a></li>
                        <li><a href="{{ route('surat.ekspedisi.index') }}" class="nav-link text-white">Ekspedisi</a></li>
                        <li><a href="{{ route('surat.cleaning.index') }}"
                                class="nav-link text-white">Cleaning Services</a></li>
                    </ul>
                </div>
            </li>
        @endrole


        @if (auth()->check() &&
                !auth()->user()->hasRole('ekspedisi') &&
                !auth()->user()->hasRole('cleaning_services') &&
                !auth()->user()->hasRole('admin'))
            <li class="nav-item">
                <a href="{{ route('omsets.index') }}"
                    class="nav-link text-white {{ Request::routeIs('omsets.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-dollar-sign"></i>
                    <p>Omset</p>
                </a>
            </li>
             <li class="nav-item">
                <a href="{{ route('klien.index') }}"
                    class="nav-link text-white {{ Request::routeIs('klien.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-user"></i>
                    <p>Klien</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('progress_projects.index') }}"
                    class="nav-link text-white {{ Request::routeIs('progress_projects.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-tasks"></i>
                    <p>Progress Project</p>
                </a>
            </li>

            <li class="nav-item">
                <a href="{{ route('maintenances.index') }}"
                    class="nav-link text-white {{ Request::routeIs('maintenances.index') ? 'active' : '' }}">
                    <i class="nav-icon fas fa-cogs"></i>
                    <p>Maintenance</p>
                </a>
            </li>
        @endif


        <!-- Tombol Logout di bawah tapi tidak terlalu mepet -->
        <form method="POST" action="{{ route('logout') }}" class="text-center mt-auto mb-3"
            style="position: relative; bottom: 200px; width: 100%;">
            @csrf
            <button type="submit" class="btn btn-danger w-100">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </form>
</nav>
