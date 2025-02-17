<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="index3.html" class="brand-link">
        <!-- <img src="{{ asset('images/INKAICON.png') }}" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8"> -->
        <span class="brand-text font-weight-light">Technology Office</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar user panel (optional) -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="{{ asset('images/usericon1.png') }}" class="img-circle elevation-2" alt="User Image">
            </div>
            @guest


            @else


                <div class="info">
                    <a href="{{ url('update-informasi') }}" class="d-block">{{$userdef->username}}</a>
                    <a href="{{ url('update-informasi') }}" class="d-block">{{$unitsingkatan}}</a>
                </div>
            @endguest
        </div>

        <!-- SidebarSearch Form -->
        <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class with font-awesome or any other icon font library -->
                @guest


                    <!-- Tidak ada yang ditampilkan ketika pengguna tidak masuk -->
                @else


                                @php
                                    $status = $userdefrule;
                                @endphp

                                <li class="nav-item">
                                    <a href="{{ url('') }}" class="nav-link {{ Request::is('/') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-tachometer-alt"></i>
                                        <p>Dashboard</p>
                                    </a>
                                </li>

                                <li class="nav-item menu-close {{ Request::is('notification') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('search') || Request::is('mail/receive/' . $status) || Request::is('all-users') || Request::is('massuploaduser') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-user"></i>
                                        <p>
                                            Inbox
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        <li class="nav-item">
                                            <a href="{{ url('notification/receive/' . $status) }}"
                                                class="nav-link {{ Request::is('notification/receive/' . $status) ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-envelope"></i>
                                                <p>Mailbox (Sementara) </p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('notification/viewsendwa') }}"
                                                class="nav-link {{ Request::is('notification/viewsendwa') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-envelope"></i>
                                                <p>Send Wa by system (Sementara) </p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                <li
                                    class="nav-item menu-close {{ Request::is('search') || Request::is('mail/receive/' . $status) || Request::is('users') || Request::is('massuploaduser') ? 'menu-open' : '' }}">
                                    <a href="#"
                                        class="nav-link {{ Request::is('search') || Request::is('mail/receive/' . $status) || Request::is('all-users') || Request::is('massuploaduser') ? 'active' : '' }}">
                                        <i class="nav-icon fas fa-user"></i>
                                        <p>
                                            User
                                            <i class="right fas fa-angle-left"></i>
                                        </p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @if(in_array($userdefrule, ["superuser"]))
                                            <li class="nav-item">
                                                <a href="{{ url('search') }}" class="nav-link {{ Request::is('search') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-search"></i>
                                                    <p>Search</p>
                                                </a>
                                            </li>
                                        @endif


                                        <li class="nav-item">
                                            <a href="{{ url('mail/receive/' . $status) }}"
                                                class="nav-link {{ Request::is('mail/receive/' . $status) ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-envelope"></i>
                                                <p>Mailbox</p>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a href="{{ url('users') }}" class="nav-link {{ Request::is('users') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-users"></i>
                                                <p>Anggota Divisi Teknologi</p>
                                            </a>
                                        </li>
                                        @can('adminsetting')
                                            <li class="nav-item">
                                                <a href="{{ url('massuploaduser') }}"
                                                    class="nav-link {{ Request::is('massuploaduser') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-file-upload"></i>
                                                    <p>Upload Massal Anggota</p>
                                                </a>
                                            </li>
                                        @endcan

                                        <li class="nav-item">
                                            <a href="{{ url('innovation-progress') }}"
                                                class="nav-link {{ Request::is('innovation-progress') ? 'active' : '' }}">
                                                <i class="nav-icon fas fa-search"></i>
                                                <p>Inovasi Digital</p>
                                            </a>
                                        </li>
                                    </ul>
                                </li>

                                @can('Memo')
                                    <li
                                        class="nav-item menu-close {{ Request::is("new-memo/upload") || Request::is("new-memo") ? 'menu-open' : '' }}">
                                        <a href="#"
                                            class="nav-link {{ Request::is("new-memo/upload") || Request::is("new-memo") ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-calendar-check"></i>
                                            <p>
                                                Memo Approval
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            @if($nama_divisi != "Logistik")
                                                <li class="nav-item">
                                                    <a href="{{ url('new-memo/upload') }}"
                                                        class="nav-link {{ Request::is("new-memo/upload") ? 'active' : '' }}">
                                                        <i class="nav-icon fas fa-upload"></i>
                                                        <p>Upload Memo Approval</p>
                                                    </a>
                                                </li>
                                            @endif


                                            <li class="nav-item">
                                                <a href="{{ route('new-memo.index') }}"
                                                    class="nav-link {{ Request::is("new-memo/terbuka") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-map-marker-alt"></i>
                                                    <p>Mapping Memo Approval Terbuka</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('new-memo.indextertutup') }}"
                                                    class="nav-link {{ Request::is("new-memo/tertutup") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-map-marker-alt"></i>
                                                    <p>Mapping Memo Approval Tertutup</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan

                                @can('Justimemo')
                                    <li
                                        class="nav-item menu-close {{ Request::is("justi-memo/upload") || Request::is("justi-memo") ? 'menu-open' : '' }}">
                                        <a href="#"
                                            class="nav-link {{ Request::is("justi-memo/upload") || Request::is("justi-memo") ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-clipboard-list"></i>
                                            <p>
                                                Memo Justifikasi (Soon)
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ url('justi-memo/upload') }}"
                                                    class="nav-link {{ Request::is("justi-memo/upload") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-upload"></i>
                                                    <p>Upload Memo Justifikasi (Soon)</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('justi-memo') }}"
                                                    class="nav-link {{ Request::is("justi-memo") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-map-marker-alt"></i>
                                                    <p>Mapping Memo Justifikasi (Soon)</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan

                                @can('Ramsdocument')
                                    <li class="nav-item menu-close {{ Request::is("rams/documents*") ? 'menu-open' : '' }}">
                                        <a href="#" class="nav-link {{ Request::is("rams/documents*") ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file"></i>
                                            <p>
                                                Rams Dokumen
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ url('rams/create') }}"
                                                    class="nav-link {{ Request::is("rams/documents/create") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-plus"></i>
                                                    <p>Buat Dokumen</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('rams/') }}"
                                                    class="nav-link {{ Request::is("rams/documents") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-file-alt"></i>
                                                    <p>Mapping Dokumen</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan



                                @can('Newbom')
                                    <li
                                        class="nav-item menu-close {{ Request::is("newboms") || Request::is("newboms/upload/excel") ? 'menu-open' : '' }}">
                                        <a href="#"
                                            class="nav-link {{ Request::is("newboms") || Request::is("newboms/upload/excel") ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-cogs"></i>
                                            <p>
                                                BOM
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('newbom.searchkomat') }}"
                                                    class="nav-link {{ Request::is('search') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-search"></i> <!-- Ikon pencarian -->
                                                    <p>Cari Kode Material</p>
                                                </a>
                                            </li>

                                            <li class="nav-item">
                                                <a href="{{route('uploadnewbom.form') }}"
                                                    class="nav-link {{ Request::is("newboms/upload/excel") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-file-upload"></i>
                                                    <p>Upload BOM</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('newboms') }}"
                                                    class="nav-link {{ Request::is("newboms") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-sitemap"></i>
                                                    <p>Mapping BOM</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan


                                @can('Progress')
                                    <li
                                        class="nav-item menu-close {{ Request::is("newprogressreports/upload") || Request::is("newreports") ? 'menu-open' : '' }}">
                                        <a href="#"
                                            class="nav-link {{ Request::is("newprogressreports/upload") || Request::is("newreports") ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-tasks"></i> <!-- Ikon untuk Progres Dokumen -->
                                            <p>
                                                Progres Dokumen
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('newprogressreports.searchform') }}"
                                                    class="nav-link {{ Request::is('newprogressreports/search') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-search"></i> <!-- Ikon pencarian -->
                                                    <p>Cari Dokumen</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('newprogressreports.document-kindindex') }}"
                                                    class="nav-link {{ Request::is('newprogressreports/document-kind') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-file-alt"></i> <!-- Ikon untuk Jenis Dokumen -->
                                                    <p>Jenis Dokumen</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('newprogressreports.index-notif-harian-units') }}"
                                                    class="nav-link {{ Request::is('newprogressreports/notif-harian-units') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-file-alt"></i> <!-- Ikon untuk Jenis Dokumen -->
                                                    <p>Notif Dokumen</p>
                                                </a>
                                            </li>
                                            @can('UploadProgressDocument')
                                                <li class="nav-item">
                                                    <a href="{{ url('newprogressreports/upload') }}"
                                                        class="nav-link {{ Request::is("newprogressreports/upload") ? 'active' : '' }}">
                                                        <i class="nav-icon fas fa-file-upload"></i> <!-- Ikon untuk Upload Progres Dokumen -->
                                                        <p>Upload Progres Dokumen</p>
                                                    </a>
                                                </li>
                                            @endcan
                                            <li class="nav-item">
                                                <a href="{{ url('newreports') }}"
                                                    class="nav-link {{ Request::is("newreports") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-map"></i> <!-- Ikon untuk Mapping Progres Dokumen -->
                                                    <p>Mapping Progres Dokumen</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('newreports.target') }}"
                                                    class="nav-link {{ Request::is("newreports/ganttchart/target/chart") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-map"></i> <!-- Ikon untuk Mapping Progres Dokumen -->
                                                    <p>Schedule</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('newreports.jamorang') }}"
                                                    class="nav-link {{ Request::is("newreports/areachart/jamorang/chart") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-map"></i> <!-- Ikon untuk Mapping Progres Dokumen -->
                                                    <p>Jam Orang</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan




                                @can('Jobticket')
                                            <li
                                                class="nav-item menu-close {{ Request::is('jobticket/uploadexcel') || Request::is('jobticket') ? 'menu-open' : '' }}">
                                                <a href="#"
                                                    class="nav-link {{ Request::is('jobticket/uploadexcel') || Request::is('jobticket') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-clipboard-list"></i> <!-- Ikon untuk Jobticket -->
                                                    <p>
                                                        Jobticket
                                                        <i class="right fas fa-angle-left"></i>
                                                    </p>
                                                </a>
                                                <ul class="nav nav-treeview">
                                                    <li class="nav-item">
                                                        <a href="{{ route('jobticket.jobticket-document-kindindex') }}"
                                                            class="nav-link {{ Request::is('jobticket/jobticket-document-kind') ? 'active' : '' }}">
                                                            <i class="nav-icon fas fa-file-alt"></i> <!-- Ikon untuk Jenis Dokumen -->
                                                            <p>Jenis Dokumen</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ route('jobticket.uploadexcel') }}"
                                                            class="nav-link {{ Request::is('jobticket/uploadexcel') ? 'active' : '' }}">
                                                            <i class="nav-icon fas fa-file-upload"></i> <!-- Ikon untuk Upload Progres Dokumen -->
                                                            <p>Upload Progres Dokumen</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ route('jobticket.showdocumentselfterbuka') }}"
                                                            class="nav-link {{ Request::is('jobticket/showself/terbuka') ? 'active' : '' }}">
                                                            <i class="nav-icon fas fa-folder-open"></i> <!-- Ikon untuk Self Drafter (Terbuka) -->
                                                            <p>Self Jobticket (Terbuka)</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ route('jobticket.showdocumentselftertutup') }}"
                                                            class="nav-link {{ Request::is('jobticket/showself/tertutup') ? 'active' : '' }}">
                                                            <i class="nav-icon fas fa-folder"></i> <!-- Ikon untuk Self Drafter (Tertutup) -->
                                                            <p>Self Jobticket (Tertutup)</p>
                                                        </a>
                                                    </li>
                                                    <li class="nav-item">
                                                        <a href="{{ route('jobticket.showunit') }}"
                                                            class="nav-link {{ Request::is('jobticket/unit') ? 'active' : '' }}">
                                                            <i class="nav-icon fas fa-sitemap"></i> <!-- Ikon untuk Mapping Jobticket -->
                                                            <p>Mapping Jobticket</p>
                                                        </a>
                                                    </li>
                                                    @if(auth()->user()->rule && str_contains(auth()->user()->rule, 'Manager'))
                                                        <li class="nav-item">
                                                            <a href="{{ route('jobticket.managershow') }}"
                                                                class="nav-link {{ Request::is('jobticket/manager/terbuka') ? 'active' : '' }}">
                                                                <i class="nav-icon fas fa-tasks"></i> <!-- Ikon baru untuk Manager Task -->
                                                                <p>Manager Task</p>
                                                            </a>
                                                        </li>
                                                    @endif




                                                    @php
                                                        // Ambil rule pengguna
                                                        $userRule = auth()->user()->rule;

                                                        // Modifikasi rule berdasarkan kondisi
                                                        if (Str::contains($userRule, 'Manager')) {
                                                            $userRule = Str::replace('Manager', '', $userRule);
                                                        }
                                                        if ($userRule === 'Senior Manager Engineering') {
                                                            $userRule = 'Quality Engineering';
                                                        }
                                                    @endphp

                                                    <li class="nav-item">
                                                        <a href="{{ route('jobticket.rank', ['unit' => trim($userRule)]) }}"
                                                            class="nav-link {{ Request::is('jobticket/rank') ? 'active' : '' }}">
                                                            <i class="nav-icon fas fa-chart-line"></i> <!-- Ikon untuk Performa -->
                                                            <p>Performa</p>
                                                        </a>
                                                    </li>



                                                </ul>
                                            </li>
                                @endcan


                                @can('KatalogKomat')
                                    <li class="nav-item menu-close {{ Request::is('katalogkomat*') ? 'menu-open' : '' }}">
                                        <a href="#" class="nav-link {{ Request::is('katalogkomat*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-alt"></i>
                                            <p>
                                                Katalog Kode Material
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('katalogkomat.index') }}"
                                                    class="nav-link {{ Request::is('katalogkomat') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-list"></i>
                                                    <p>Daftar Katalog Kode Material</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('katalogkomat.formexcel') }}"
                                                    class="nav-link {{ Request::is('katalogkomat/uploadexcel') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-upload"></i>
                                                    <p>Unggah Data Excel</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan


                                @can('Library')
                                    <li class="nav-item menu-close {{ Request::is('file-management*') ? 'menu-open' : '' }}">
                                        <a href="#" class="nav-link {{ Request::is('file-management*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-file-alt"></i>
                                            <p>
                                                Library
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ route('library.index') }}"
                                                    class="nav-link {{ Request::is('file-management') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-list"></i>
                                                    <p>Daftar Library</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('library.create') }}"
                                                    class="nav-link {{ Request::is('file-management/create') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-upload"></i>
                                                    <p>Unggah Library</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan


                                @can('Rapat')
                                    <li class="nav-item menu-close {{ Request::is('events*') ? 'menu-open' : '' }}">
                                        <a href="#" class="nav-link {{ Request::is('events*') ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-calendar-alt"></i>
                                            <p>
                                                Ruang Rapat
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="https://drive.google.com/drive/u/0/folders/13sCkaK6ZIxwooWbEbQnBNSFkIJwturye?ths=true"
                                                    class="nav-link">
                                                    <i class="nav-icon fas fa-book"></i>
                                                    <p>User Manual</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('events.create') }}"
                                                    class="nav-link {{ Request::is('events/create') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-calendar-plus"></i>
                                                    <p>Buat Jadwal</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('events.all') }}"
                                                    class="nav-link {{ Request::is('events/all') ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-map-marker-alt"></i>
                                                    <p>Mapping Rapat</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan



                                @can('adminsetting')
                                    <li
                                        class="nav-item menu-close {{ Request::is("document") || Request::is("file") || Request::is("categories") ? 'menu-open' : '' }}">
                                        <a href="#"
                                            class="nav-link {{ Request::is("document") || Request::is("file") || Request::is("categories") ? 'active' : '' }}">
                                            <i class="nav-icon fas fa-building"></i>
                                            <p>
                                                Data Kantor
                                                <i class="right fas fa-angle-left"></i>
                                            </p>
                                        </a>
                                        <ul class="nav nav-treeview">
                                            <li class="nav-item">
                                                <a href="{{ url('document') }}"
                                                    class="nav-link {{ Request::is("document") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-file"></i>
                                                    <p>Dokumen</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('file') }}" class="nav-link {{ Request::is("file") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-file"></i>
                                                    <p>File</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('categories') }}"
                                                    class="nav-link {{ Request::is("categories") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-list"></i>
                                                    <p>Kategori</p>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('zoom') }}" class="nav-link {{ Request::is("zoom") ? 'active' : '' }}">
                                                    <i class="nav-icon fas fa-list"></i>
                                                    <p>Zoom</p>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                @endcan

                @endguest
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>