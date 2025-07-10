@extends('layout.main')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
      <div class="col-md-12">
        <div class="card mb-6">
          <!-- Account -->
          <h5 class="card-header pt-4">Pengaturan Akun</h5>
          <div class="card-body">
            <form action="{{ route('master/aktor/pengguna.update', auth()->user()->id) }}" id="formAccountSettings" method="POST">
              @method('PUT')
              @csrf
              <div class="row g-6">
                <div class="col-md-6">
                  <label for="name" class="form-label">Nama Lengkap</label>
                  <input
                    class="form-control"
                    type="text"
                    id="name"
                    name="name"
                    value="{{ Auth::user()->name ?? '' }}"
                    autofocus />
                </div>
                <div class="col-md-6">
                  <label for="username" class="form-label">Username</label>
                  <input
                    class="form-control"
                    type="text"
                    id="username"
                    name="username"
                    value="{{ Auth::user()->username ?? '' }}"/>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">E-mail</label>
                  <input
                    class="form-control"
                    type="text"
                    id="email"
                    name="email"
                    value="{{ Auth::user()->email ?? '-' }}"
                    placeholder="example@example.com" />
                </div>
                <div class="col-md-6 form-password-toogle">
                    <label class="form-label" for="password">Password *</label>
                      <div class="input-group input-group-merge">
                          <input 
                          type="password" 
                          class="form-control" 
                          id="password" 
                          name="password"
                          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                          aria-describedby="password"
                            />
                          <span class="input-group-text cursor-pointer"><i class="icon-base bx bx-hide"></i></span>
                      </div>
                      <div class="text-danger small fst-italic">Kosongkan jika tidak ingin mengubah password</div>
                </div>
              </div>
              <div class="mt-6">
                <button type="submit" class="btn btn-primary me-3">Save Changes</button>
              </div>
            </form>
          </div>
          <!-- /Account -->
        </div>

        @if (Auth::user()->role === 'Guru' && Auth::user()->teacher)
          <div class="card">
            <h5 class="card-header">Data Guru</h5>
            <div class="card-body">
              <form id="formTeacherUpdate" method="POST" action="{{ route('update/profile/guru.updateProfile', encrypt(auth()->user()->teacher->id ?? '')) }}">
                @method('PUT')
                @csrf
                <div class="row g-6">
                  <div class="col-md-6">
                    <label for="nip" class="form-label">Nomor Induk Pegawai</label>
                    <input
                      class="form-control"
                      type="text"
                      maxlength="18"
                      id="nip"
                      name="nip"
                      value="{{ Auth::user()->teacher->nip ?? '' }}"
                      oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                      autofocus />
                  </div>
                  <div class="col-md-6">
                    <label for="name" class="form-label">Nama Lengkap</label>
                    <input
                      class="form-control"
                      type="text"
                      id="name"
                      name="name"
                      value="{{ Auth::user()->teacher->name ?? '' }}"
                      autofocus />
                  </div>
                  <div class="col-md-6">
                    <label for="name" class="form-label">Kode Guru</label>
                    <input
                      class="form-control"
                      type="text"
                      id="code"
                      name="code"
                      value="{{ Auth::user()->teacher->code ?? '' }}"
                      />
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="jenis_kelamin">Jenis Kelamin *</label>
                      <select name="jenis_kelamin" class="form-control" id="jenis_kelamin" required>
                          @foreach ($arrJk as $jk)
                              @if ((Auth::user()->teacher->jenis_kelamin ?? '') === $jk)
                                  <option value="{{ $jk }}" selected>{{ $jk }}</option>
                              @else
                                  <option value="{{ $jk }}">{{ $jk }}</option>
                              @endif
                          @endforeach
                      </select>
                  </div>
                  <div class="col-md-6">
                      <label class="form-label" for="tempat_lhr">Tempat / Tanggal Lahir</label>
                      <div class="input-group">
                          <input type="text" class="form-control" name="tempat_lhr" id="tempat_lhr" value="{{ Auth::user()->teacher->tempat_lhr ?? '' }}" placeholder="Birth Place" />
                          <span class="input-group-text" id="basic-addon13">/</span>
                          <input type="date" class="form-control" name="tanggal_lhr" id="tanggal_lhr" value="{{ Auth::user()->teacher->tanggal_lhr ?? '' }}" placeholder="Birth Place" />
                      </div>
                  </div>
                  <div class="col-md-6">
                    <label for="mapel" class="form-label">Mapel Bidang</label>
                    <input
                      class="form-control"
                      type="text"
                      id="mapel"
                      disabled
                      value="{{ Auth::user()->teacher->subject->name ?? '-' }}"/>
                  </div>
                </div>
                <div class="mt-6">
                  <button type="submit" class="btn btn-primary me-3">Save changes</button>
                </div>
              </form>
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
@endsection