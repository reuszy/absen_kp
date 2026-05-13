<nav class="sidebar sidebar-offcanvas" id="sidebar">
  <div class="sidebar-brand-wrapper d-none d-lg-flex align-items-center justify-content-center fixed-top">
    <a class="sidebar-brand brand-logo" href="#" style="color:white; text-decoration:none;">
      <img src="{{ asset('assets/images/logo_ugj.jpg') }}" alt="logo" style="height: auto; width: 100%; max-width: 150px;" />
    </a>
    <a class="sidebar-brand brand-logo-mini" href="#" style="color:white; text-decoration:none;">
      <img src="{{ asset('assets/images/logo_mini_ugj.jpg') }}" alt="logo" style="height: auto; width: 100%; max-width: 40px;" />
    </a>
  </div>
  <ul class="nav">
    <li class="nav-item profile">
      <div class="profile-desc">
        <div class="profile-pic">
          <div class="count-indicator">
            <img class="img-xs rounded-circle " src="{{ asset('assets/images/faces/face15.jpg') }}" alt="">
            <span class="count bg-success"></span>
          </div>
          <div class="profile-name">
            <h5 class="mb-0 font-weight-normal">{{ Auth::user()->nama }}</h5>
            <span>{{ ucfirst(Auth::user()->role) }}</span>
          </div>
        </div>
      </div>
    </li>
    <li class="nav-item nav-category">
      <span class="nav-link">Menu Utama</span>
    </li>
    
    <li class="nav-item menu-items {{ request()->routeIs('dashboard') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('dashboard') }}">
        <span class="menu-icon">
          <i class="mdi mdi-speedometer"></i>
        </span>
        <span class="menu-title">Dashboard</span>
      </a>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('attendance.rekap') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('attendance.rekap') }}">
        <span class="menu-icon">
          <i class="mdi mdi-table-large"></i>
        </span>
        <span class="menu-title">Rekap Absensi</span>
      </a>
    </li>

    @if (Auth::user()->isStaff())
    <li class="nav-item nav-category">
      <span class="nav-link">Data</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('staf.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('staf.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-card-account-details"></i>
        </span>
        <span class="menu-title">Data Staf</span>
      </a>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('leaves.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-calendar-remove"></i>
        </span>
        <span class="menu-title">Izin & Cuti</span>
      </a>
    </li>


    <li class="nav-item nav-category">
      <span class="nav-link">Pengaturan</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('account.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('account.show') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-cog text-info"></i>
        </span>
        <span class="menu-title">Pengaturan Akun</span>
      </a>
    </li>


    <li class="nav-item nav-category">
      <span class="nav-link">Log Absensi</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('attendance.logs') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('attendance.logs') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-box"></i>
        </span>
        <span class="menu-title">Log Absen</span>
      </a>
    </li>
    
    @endif


    @if(Auth::user()->isAdmin())
    <li class="nav-item nav-category">
      <span class="nav-link">Data</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('staf.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('staf.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-card-account-details"></i>
        </span>
        <span class="menu-title">Data Staf</span>
      </a>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('leaves.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('leaves.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-calendar-remove"></i>
        </span>
        <span class="menu-title">Izin & Cuti</span>
      </a>
    </li>


    <li class="nav-item nav-category">
      <span class="nav-link">Pengaturan</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('user.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('user.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-key"></i>
        </span>
        <span class="menu-title">Manajemen User</span>
      </a>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('settings.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('settings.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-cogs"></i>
        </span>
        <span class="menu-title">Manajemen Shift</span>
      </a>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('fakultas.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('fakultas.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-domain"></i>
        </span>
        <span class="menu-title">Manajemen Fakultas</span>
      </a>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('fingerprint.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('fingerprint.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-fingerprint"></i>
        </span>
        <span class="menu-title">Perangkat Fingerprint</span>
      </a>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('account.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('account.show') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-cog text-info"></i>
        </span>
        <span class="menu-title">Pengaturan Akun</span>
      </a>
    </li>


    <li class="nav-item nav-category">
      <span class="nav-link">Log Absensi</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('attendance.logs') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('attendance.logs') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-box"></i>
        </span>
        <span class="menu-title">Log Absen</span>
      </a>
    </li>

    <li class="nav-item nav-category">
      <span class="nav-link">Tools</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('simulasi.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('simulasi.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-play-circle-outline"></i>
        </span>
        <span class="menu-title">Simulasi Absen</span>
      </a>
    </li>
    @endif

  </ul>
</nav>