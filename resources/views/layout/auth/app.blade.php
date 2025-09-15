<!doctype html>

<html
  lang="en"
  class="layout-wide customizer-hide"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
    <head>
        <!-- Page CSS -->
        <!-- Page -->
        @push('page-css')
            <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/page-auth.css') }}" />
        @endpush

        @include('layout.styles')
    </head>

  <body>
    <!-- Content -->
    @yield('loginContent')
    <!-- / Content -->

    @include('layout.scripts')

    @push('page-js')
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

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    text: "{{ session('success') }}"
                });
            @elseif(session('error'))
                Toast.fire({
                    icon: 'error',
                    text: "{{ session('error') }}"
                });
            @endif
        </script>
    @endpush

  </body>
</html>
