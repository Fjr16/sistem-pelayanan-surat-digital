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
                  <label for="name" class="form-label">Nama Lengkap *</label>
                  <input
                    class="form-control"
                    type="text"
                    id="name"
                    name="name"
                    value="{{ Auth::user()->name ?? '' }}"
                    autofocus
                    required/>
                </div>
                <div class="col-md-6">
                  <label for="username" class="form-label">Username *</label>
                  <input
                    class="form-control"
                    type="text"
                    id="username"
                    name="username"
                    value="{{ Auth::user()->username ?? '' }}"
                    required/>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">E-mail *</label>
                  <input
                    class="form-control"
                    type="text"
                    id="email"
                    name="email"
                    value="{{ Auth::user()->email ?? '-' }}"
                    placeholder="example@example.com"
                    required/>
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
                <div class="col-md-6">
                    <label for="nik" class="form-label">Nomor Induk Kependudukan (NIK) *</label>
                    <input
                      class="form-control"
                      type="text"
                      maxlength="16"
                      id="nik"
                      name="nik"
                      value="{{ Auth::user()->nik ?? '' }}"
                      oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                      autofocus
                      required/>
                </div>
                <div class="col-md-6">
                    <label for="no_kk" class="form-label">Nomor Kartu Keluarga (KK) *</label>
                    <input
                    class="form-control"
                    type="text"
                    maxlength="20"
                    id="no_kk"
                    name="no_kk"
                    value="{{ Auth::user()->no_kk ?? '' }}"
                    oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                    required/>
                </div>
                <div class="col-md-6">
                <label for="no_wa" class="form-label">Nomor WA * <br>
                </label>
                <input
                    class="form-control"
                    type="tel"
                    id="no_wa"
                    name="no_wa"
                    value="{{ Auth::user()->no_wa }}"
                    required
                    />
                    <div class="">
                        <small class="fst-italic text-danger">Pastikan nomor memiliki WA yang aktif, karena surat akan dikirim ke nomor ini</small>
                    </div>
                </div>
                <div class="col-md-6">
                <label for="name" class="form-label">Nama Lengkap *</label>
                <input
                    class="form-control"
                    type="text"
                    id="name"
                    name="name"
                    value="{{ Auth::user()->name ?? '' }}"
                    autofocus
                    required />
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="gender">Jenis Kelamin *</label>
                    <select name="gender" class="form-control" id="gender" required>
                        @foreach ($arrJk as $jk)
                            @if ((Auth::user()->gender ?? '') === $jk)
                                <option value="{{ $jk }}" selected>{{ $jk }}</option>
                            @else
                                <option value="{{ $jk }}">{{ $jk }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="tempat_lhr">Tempat / Tanggal Lahir *</label>
                    <div class="input-group">
                        <input type="text" class="form-control" name="tempat_lhr" id="tempat_lhr" value="{{ Auth::user()->tempat_lhr ?? '' }}" placeholder="Birth Place" required />
                        <span class="input-group-text" id="basic-addon13">/</span>
                        <input type="date" class="form-control" name="tanggal_lhr" id="tanggal_lhr" value="{{ Auth::user()->tanggal_lhr ?? '' }}" placeholder="Birth Date" required />
                    </div>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="agama">Agama *</label>
                    <select name="agama" class="form-control" id="agama" required>
                        @foreach ($agama as $item)
                            @if ((Auth::user()->agama ?? '') === $item->value)
                                <option value="{{ $item->value }}" selected>{{ $item->label() }}</option>
                            @else
                                <option value="{{ $item->value }}">{{ $item->label() }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label" for="status_kawin">Status Kawin *</label>
                    <select name="status_kawin" class="form-control" id="status_kawin" required>
                        @foreach ($maritalStts as $item)
                            @if ((Auth::user()->status_kawin ?? '') === $item->value)
                                <option value="{{ $item->value }}" selected>{{ $item->label() }}</option>
                            @else
                                <option value="{{ $item->value }}">{{ $item->label() }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                <label for="pekerjaan" class="form-label">Pekerjaan</label>
                <input
                    class="form-control"
                    type="text"
                    id="pekerjaan"
                    name="pekerjaan"
                    value="{{ Auth::user()->pekerjaan ?? '-' }}"/>
                </div>
                <div class="col-md-6">
                <label for="jabatan" class="form-label">Jabatan</label>
                <input
                    class="form-control"
                    type="text"
                    id="jabatan"
                    name="jabatan"
                    value="{{ Auth::user()->jabatan ?? '-' }}"/>
                </div>
                @canany(['petugas', 'sekretaris', 'wali-nagari'])
                <div class="col-md-6">
                <label for="tanggal_masuk" class="form-label">Tanggal Masuk</label>
                <input
                    class="form-control"
                    type="date"
                    id="tanggal_masuk"
                    name="tanggal_masuk"
                    value="{{ Auth::user()->tanggal_masuk ?? '-' }}"/>
                </div>
                @endcanany
                <div class="col-md-6">
                    <label for="alamat_ktp" class="form-label">Alamat KTP</label>
                    <textarea
                        class="form-control"
                        name="alamat_ktp"
                        id="alamat_ktp"
                        rows="2" placeholder="Alamat sesuai KTP">{{ Auth::user()->alamat_ktp ?? '' }}
                    </textarea>
                </div>
                <div class="col-md-6">
                    <label for="alamat_dom" class="form-label">Alamat Domisili</label>
                    <textarea
                        class="form-control"
                        name="alamat_dom"
                        id="alamat_dom"
                        rows="2" placeholder="Alamat sesuai Domisili">{{ Auth::user()->alamat_dom ?? '' }}
                    </textarea>
                </div>
              </div>
              <div class="mt-6">
                <button type="submit" class="btn btn-primary me-3">Save Changes</button>
              </div>
            </form>
          </div>
          <!-- /Account -->
        </div>
      </div>
    </div>
  </div>
@endsection
