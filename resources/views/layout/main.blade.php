<!doctype html>

<html
  lang="en"
  class="layout-menu-fixed layout-compact"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
    @include('layout.styles')
    {{-- data tables --}}
    <link rel="stylesheet" href="{{ asset('assets/vendor/libs/dataTables/datatables.min.css') }}">
    <!-- Datepicker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        @include('layout.sidebar')
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          @include('layout.navbar')
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <div class="container-xxl container-p-y pb-0">
              <!-- flash Message -->
              @php
                  $class = 'success';
                  if (!session()->has('success')) {
                    $class = 'danger';
                  }
              @endphp
              @if (session('success') || session('error'))
                <div class="alert alert-{{ $class }}" role="alert">
                  <div class="d-flex">
                    <i class="bx bx-{{ session('success') ? 'check-shield' : 'shield-x' }} me-2" style="font-size: 150%"></i>
                    <span>
                      {{ session('success') ?? session('error') }}
                    </span>
                  </div>
                </div>
              @endif
              <!-- / flash Message -->
            </div>
            <!-- Content -->
            @yield('content')
            <!-- / Content -->

            <!-- Footer -->
            @include('layout.footer')
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->


    {{-- scripts --}}
    {{-- vendor js --}}
    @push('vendor-js')
        <script src="{{ asset('/assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
        {{-- datatables --}}
        <script src="{{ asset('/assets/vendor/libs/dataTables/datatables.min.js') }}"></script>
        {{-- datepicker --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    @endpush
    {{-- page js --}}
    @push('page-js')
        <script src="{{ asset('/assets/js/dashboards-analytics.js') }}"></script>

        {{-- alert auto close --}}
        <script>
          $(document).ready(function() {
            setTimeout(function() {
              $('.alert').fadeOut();
            }, 3000);
          });
        </script>

        {{-- inisialisasi data table --}}
        <script>
          $('.dataTable').DataTable();
        </script>
        {{-- sweetalert --}}
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
          // toast pesan success
          const Toast = Swal.mixin({
            toast: true,
            position: "top-end",
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
              toast.onmouseenter = Swal.stopTimer;
              toast.onmouseleave = Swal.resumeTimer;
            }
          });
        </script>
    @endpush

    @include('layout.scripts')


  </body>
</html>
