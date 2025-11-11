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
<!-- Page JS -->
@stack('page-js')

<!-- Place this tag before closing body tag for github widget button. -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
