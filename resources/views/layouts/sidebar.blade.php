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
            <h5 class="mb-0 font-weight-normal">Admin Kampus</h5>
            <span>Administrator</span>
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
    
     <li class="nav-item menu-items {{ request()->routeIs('attendance.logs') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('attendance.logs') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-box"></i>
        </span>
        <span class="menu-title">Log Absen</span>
      </a>
    </li>

    <li class="nav-item nav-category">
      <span class="nav-link">Pengaturan</span>
    </li>

    <li class="nav-item menu-items {{ request()->routeIs('staf.*') ? 'active' : '' }}">
      <a class="nav-link" href="{{ route('staf.index') }}">
        <span class="menu-icon">
          <i class="mdi mdi-account-group"></i>
        </span>
        <span class="menu-title">Management Staf</span>
      </a>
    </li>

  </ul>
</nav>