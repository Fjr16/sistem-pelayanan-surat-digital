<!-- Core JS -->

<script src="{{ asset('/assets/vendor/libs/jquery/jquery.js') }}"></script>

<script src="{{ asset('/assets/vendor/libs/popper/popper.js') }}"></script>
<script src="{{ asset('/assets/vendor/js/bootstrap.js') }}"></script>

<script src="{{ asset('/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>

<script src="{{ asset('/assets/vendor/js/menu.js') }}"></script>

<!-- endbuild -->

<!-- Vendors JS -->
@stack('vendor-js')

<!-- Main JS -->

<script src="{{ asset('/assets/js/main.js') }}"></script>

<!-- Page JS -->
@stack('page-js')

<!-- Place this tag before closing body tag for github widget button. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>