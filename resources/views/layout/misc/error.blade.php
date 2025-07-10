<!doctype html>

<html lang="en" class="layout-wide" data-assets-path="../assets/" data-template="vertical-menu-template-free">
    <head>
        <!-- Page CSS -->
        <!-- Page -->
        @push('page-css')
            <link rel="stylesheet" href="{{ asset('/assets/vendor/css/pages/page-misc.css') }}" />
        @endpush

        @include('layout.styles')
    </head>


  <body>
    <!-- Content -->
    @yield('miscContent')
    <!-- / Content -->

    {{-- scripts --}}
    @include('layout.scripts')

  </body>
</html>
