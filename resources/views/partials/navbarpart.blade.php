<nav class="main-header navbar navbar-expand navbar-white navbar-light">
  <!-- Left navbar links -->
  <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
    <li class="nav-item d-none d-sm-inline-block">
      {{-- <a href="index3.html" class="nav-link">Home</a> --}}
      <h3 class="nav-brand text-bold ml-2 mt-2"><span class="text-danger">Technology</span> Office <span
          class="badge badge-light">Divisi Teknologi</span></h3>
    </li>
    <li class="nav-item d-sm-none d-xs-inline-block">
      {{-- <a href="index3.html" class="nav-link">Home</a> --}}
      <h3 class="nav-brand text-bold ml-2 mt-2"><span class="text-danger">E</span>IS <span
          class="badge badge-light">Divisi Teknologi</span></h3>
    </li>
  </ul>

  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- Navbar Search -->
    <li class="nav-item">
      <a class="nav-link" data-widget="navbar-search" href="#" role="button">
        <i class="fas fa-search"></i>
      </a>
      <div class="navbar-search-block">
        <form class="form-inline">
          <div class="input-group input-group-sm">
            <input class="form-control form-control-navbar" type="search" placeholder="Search" aria-label="Search">
            <div class="input-group-append">
              <button class="btn btn-navbar" type="submit">
                <i class="fas fa-search"></i>
              </button>
              <button class="btn btn-navbar" type="button" data-widget="navbar-search">
                <i class="fas fa-times"></i>
              </button>
            </div>
          </div>
        </form>
      </div>
    </li>


    <!-- Messages Dropdown Menu -->
    @guest

  @else
  <li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
    <i class="nav-icon fas fa-envelope"></i>
    <span class="badge badge-danger navbar-badge">{{$tugasdivisis->count()}}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    @foreach ($tugasdivisis as $tugasdivisi)
    @if($tugasdivisi->memo)
    <a href="{{ route('new-memo.show', ['memoId' => $tugasdivisi->memo->id]) }}" class="notification-item">
      <div class="media">
      <img src="{{ asset('images/usericon1.png') }}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
      <div class="media-body">
      <h3 class="dropdown-item-title">
      {{ $tugasdivisi->unit->name }}
      <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
      </h3>
      <p class="text-sm">{{ $tugasdivisi->memo->documentnumber }} - {{ $tugasdivisi->memo->documentname }}</p>
      <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i>
      {{ $tugasdivisi->updated_at->diffForHumans() }}</p>
      </div>
      </div>
    </a>
    <div class="dropdown-divider"></div>
  @endif
  @endforeach
    <a href="{{route('notification.show', ['namadivisi' => $nama_divisi])}}" class="dropdown-item dropdown-footer">See
      All Messages</a>
    </div>

  </li>
@endguest
    <!-- Notifications Dropdown Menu
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-comments"></i>
          <span class="badge badge-warning navbar-badge">0</span>
        </a>
        
        
        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header">0 Notifications</span>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-envelope mr-2"></i> 4 new messages
            <span class="float-right text-muted text-sm">3 mins</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> 8 friend requests
            <span class="float-right text-muted text-sm">12 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-file mr-2"></i> 3 new reports
            <span class="float-right text-muted text-sm">2 days</span>
          </a>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
        </div>
      </li> -->
    <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" data-widget="control-sidebar" data-slide="true" href="#" role="button">
        <i class="fas fa-th-large"></i>
      </a>
    </li>
    <!-- Dropdown Menu untuk Pengguna Terautentikasi -->
    <li class="nav-item dropdown">
      @guest
    @else
      <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">
      <i class="fas fa-user mr-1"></i> Profil
      </a>
    @endguest
      <ul class="dropdown-menu">
        @if(!in_array($userdefrule, ['QC FAB', 'QC FIN', 'QC FAB', 'QC FIN', 'QC INC', 'Fabrikasi', 'PPC', 'QC Banyuwangi', 'Pabrik Banyuwangi', 'Fabrikasi', 'PPC']))
      <li><a href="{{ url('update-informasi') }}"><i class="lnr lnr-user"></i> <span>Profil Saya</span></a></li>
    @endif
        <!-- <li><a href="#"><i class="lnr lnr-envelope"></i> <span>Pesan</span></a></li>
              <li><a href="#"><i class="lnr lnr-cog"></i> <span>Pengaturan</span></a></li> -->
        <li><a href="{{ url('logout') }}"><i class="lnr lnr-exit"></i> <span>Logout</span></a></li>
      </ul>
    </li>
    @guest
  @else
  <li class="nav-item">
    <a class="nav-link" href="{{ url('logout') }}">
    <i class="lnr lnr-exit" role="button"></i>
    <i class="fas fa-sign-out-alt"></i>
    Logout
    </a>
  </li>
@endguest
  </ul>
</nav>