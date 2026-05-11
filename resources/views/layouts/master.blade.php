<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sistem Absensi Kampus</title>
    
    <link rel="stylesheet" href="{{ asset('assets/vendors/mdi/css/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/css/vendor.bundle.base.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" />
    <style>
        body {
            background-color: #000000; /* Fallback dark background */
        }
        .container-scroller {
            min-height: 100vh;
            display: flex; /* Force equal height columns */
        }
        
        .sidebar {
            min-height: 100vh; /* Ensure full viewport height minimum */
            background-color: #191c24; /* Match theme color so it doesn't show white if short */
            height: auto; /* Allow to grow with matching flex parent */
            flex-shrink: 0; /* Prevent shrinking */
        }

        .page-body-wrapper {
            min-height: 100vh;
            display: flex;
            flex-grow: 1;
            width: 100%;
            min-width: 0; /* CRITICAL: Allows flex item to shrink below content size */
        }

        .main-panel {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            width: 100%;
            min-width: 0; /* CRITICAL: Allows flex item to shrink below content size */
        }
        
        .content-wrapper {
            flex-grow: 1;
            width: 100%;
            min-width: 0; /* CRITICAL: Allows flex item to shrink below content size */
        }
    </style>
    @stack('styles')
  </head>
  <body class="sidebar-fixed">
    <div class="container-scroller">
      
      @include('layouts.sidebar')
      
      <div class="container-fluid page-body-wrapper">
        
        @include('layouts.navbar')

        <div class="main-panel">
          <div class="content-wrapper">
            
            @yield('content')

          </div>
          
          @include('layouts.footer')

        </div>
      </div>
    </div>
    
    <script src="{{ asset('assets/vendors/js/vendor.bundle.base.js') }}"></script>
    <script src="{{ asset('assets/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/js/misc.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script>
    @stack('scripts')
  </body>
</html>
