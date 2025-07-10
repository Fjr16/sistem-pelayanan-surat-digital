<footer class="content-footer footer bg-footer-theme">
    <div class="container-xxl">
      <div
        class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
          copyright ©
          <script>
            document.write(new Date().getFullYear());
          </script>
        </div>
        <div class="d-none d-lg-inline-block">

          <a
            href="{{ route('dashboard') }}"
            target="_blank"
            class="footer-link me-4"
            >Dashboard</a
          >

          <a
            href="{{ route('profile') }}"
            target="_blank"
            class="footer-link me-4"
            >My Profile</a
          >
        </div>
      </div>
    </div>
</footer>