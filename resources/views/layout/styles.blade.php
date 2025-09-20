<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

<title>{{ config('app.name') }} | {{ $title ?? 'Dashboard' }}</title>

<meta name="description" content="" />

<!-- Favicon -->
<link rel="icon" type="image/x-icon" href="{{ asset('assets/img/favicon/favicon.ico') }}" />

<!-- Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com" />
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
<link
    href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
    rel="stylesheet" />

<link rel="stylesheet" href="{{ asset('/assets/vendor/fonts/iconify-icons.css') }}" />

<!-- Core CSS -->
<!-- build:css assets/vendor/css/theme.css  -->

<link rel="stylesheet" href="{{ asset('/assets/vendor/css/core.css') }}" />
<link rel="stylesheet" href="{{ asset('/assets/css/demo.css') }}" />

<!-- Vendors CSS -->

<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

<!-- endbuild -->

<link rel="stylesheet" href="{{ asset('/assets/vendor/libs/apex-charts/apex-charts.css') }}" />

<!-- Page CSS -->
<style>
    .swal2-container{
        z-index: 20000 !important;
    }
    .btn-success {
    background-color: #28c76f !important;
    border-color: #28c76f !important;
    color: #fff !important;
    }

    .btn-success:hover {
    background-color: #22bb66 !important;
    border-color: #22bb66 !important;
    }

    .btn-success:focus,
    .btn-success:active {
    background-color: #1ea65c !important;
    border-color: #1ea65c !important;
    box-shadow: 0 0 0 0.25rem rgba(40, 199, 111, 0.5) !important;
    }

    .btn-success:disabled {
    background-color: #9ae6b4 !important;
    border-color: #9ae6b4 !important;
    color: #fff !important;
    opacity: 0.7 !important;
    }

    /* backround color custom */
    /* Orange */
    .bg-orange {
        background-color: #fd7e14; /* oranye */
        color: #fff;
    }

    /* Red */
    .bg-red {
        background-color: #dc3545; /* merah */
        color: #fff;
    }

    /* Blue */
    .bg-blue {
        background-color: #0d6efd; /* biru */
        color: #fff;
    }

    /* Green */
    .bg-green {
        background-color: #198754; /* hijau */
        color: #fff;
    }

    /* Purple */
    .bg-purple {
        background-color: #6f42c1; /* ungu */
        color: #fff;
    }

    /* Yellow */
    .bg-yellow {
        background-color: #ffc107; /* kuning */
        color: #212529; /* biar lebih kontras */
    }
    .bg-pink {
        background-color: #d63384;
        color: #fff;
    }
    .bg-teal {
        background-color: #20c997;
        color: #fff;
    }
    .bg-gray {
        background-color: #6c757d !important;
        color: #fff !important;
    }
    /* end  backround color custom */
</style>
@stack('page-css')


<!-- Helpers -->
<script src="{{ asset('/assets/vendor/js/helpers.js') }}"></script>
<!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

<!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->

<script src="{{ asset('/assets/js/config.js') }}"></script>
