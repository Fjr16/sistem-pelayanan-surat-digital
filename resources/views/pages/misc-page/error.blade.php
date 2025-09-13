@extends('layout.misc.error')

@section('miscContent')
    <!-- Error -->
    <div class="container-xxl container-p-y">
        <div class="misc-wrapper">
          <h1 class="mb-2 mx-2" style="line-height: 6rem; font-size: 6rem">404</h1>
          <h4 class="mb-2 mx-2">Page Not Found️ ⚠️</h4>
          <p class="mb-6 mx-2">we couldn't find the page you are looking for</p>
          <a href="{{ route('dashboard') }}" class="btn btn-primary">Back to home</a>
        </div>
      </div>
      <!-- /Error -->
@endsection