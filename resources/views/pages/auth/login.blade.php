@extends('layout.auth.app')

@section('loginContent')
<div class="container-xxl">
    <div class="authentication-wrapper authentication-basic container-p-y">
      <div class="authentication-inner">
        <!-- Register -->
        <div class="card px-sm-6 px-0">
          <div class="card-body">
            <h4 class="mb-1">{{ $title ?? '' }}! 👋</h4>
            <p class="mb-6">{{ $slug ?? '' }}</p>

            <form id="formAuthentication" class="mb-6" action="{{ route('login') }}" method="POST">
              @csrf
              <div class="mb-6">
                <label for="username" class="form-label">Username *</label>
                <input
                  type="text"
                  class="form-control @error('username') is-invalid @enderror"
                  id="username"
                  name="username"
                  placeholder="Enter your username"
                  value="{{ old('username') }}"
                  autofocus />
                  @error('username')
                    <div class="invalid-feedback">
                      {{ $message }}
                    </div>
                  @enderror
              </div>
              <div class="mb-6 form-password-toggle">
                <label class="form-label" for="password">Password *</label>
                <div class="input-group input-group-merge">
                  <input
                    type="password"
                    id="password"
                    class="form-control"
                    name="password"
                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                    aria-describedby="password" />
                  <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                </div>
              </div>
              <div class="mb-6">
                <button class="btn btn-primary d-grid w-100" type="submit">Login</button>
              </div>
            </form>

            <hr>
            <p class="text-center mt-4 mb-0">
                <span>Belum punya akun?</span>
                <a href="{{ route('register') }}" class="fw-bold">
                Daftar di sini
                </a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
