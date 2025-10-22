@extends('layout.main')

@push('page-css')
<style>
    .nav-tabs .nav-link {
        border-radius: 4px;
        font-weight: 500 !important;
        margin-right: 5px;
        padding: 6px 15px;
        transition: 0.2s;
    }
</style>
@endpush
@section('content')

    <div class="container-xxl flex-grow-1 container-p-y pt-0">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <h5>Daftar {{ $title ?? 'Pengguna' }}</h5>
                <div class="">
                    <button type="button" class="btn btn-sm btn-primary" onclick="openModalCreate()"><i class="bx bx-plus"></i> Tambah {{ $title ?? 'Pengguna' }}</button>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabs untuk role -->
                <ul class="nav nav-tabs" id="roleTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active"
                            id="tab-all"
                            data-bs-toggle="tab"
                            data-bs-target="#content-all"
                            type="button"
                            role="tab">
                            <i class="bx bx-group"></i> All Users
                            <span class="badge bg-secondary ms-1">All</span>
                        </button>
                    </li>
                    @foreach($roles as $role)
                        <li class="nav-item" role="presentation">
                            <button class="nav-link"
                                id="tab-{{ $role->value }}"
                                data-bs-toggle="tab"
                                data-bs-target="#content-all"
                                type="button"
                                role="tab">
                                <i class="bx bx-user"></i>
                                {{ $role->label() }}
                            </button>
                        </li>
                    @endforeach
                </ul>

                <div class="tab-content mt-3 px-0">
                    <div class="tab-pane fade show active" id="content-all" role="tabpanel">
                        <div class="table-responsive text-nowrap">
                            <table class="table table-sm" id="tabel-data">
                                <thead class="table-dark">
                                    <tr class="text-nowrap">
                                        <th>Action</th>
                                        <th>Nama</th>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Jenis Kelamin</th>
                                        <th>No. HP / WA</th>
                                        <th>NIK</th>
                                        <th>Tempat Tgl Lahir</th>
                                        <th>Status Kawin</th>
                                        <th>Role</th>
                                        <th>Aktif</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <x-confirm-modal/>

  <!-- Modal create -->
    <form id="formAuthenticationCreate" action="{{ route('master/aktor/pengguna.store') }}" method="POST">
        @csrf
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createModalLabel">Tambah {{ $title }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="name">Nama Lengkap *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" required/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="username">Username *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username" required/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="email">Email *</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" required/>
                            </div>
                        </div>
                        <div class="row mb-6 form-password-toogle">
                            <label class="col-sm-2 col-form-label" for="password">Password *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <input
                                    type="password"
                                    class="form-control password-field"
                                    name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password"
                                    required
                                        />
                                    <span class="input-group-text cursor-pointer toggle-password"><i class="icon-base bx bx-hide"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="role">Role *</label>
                            <div class="col-sm-10">
                                <select name="role" class="form-control" id="role" required>
                                    @foreach ($roles as $role)
                                        @if (old('role') === $role->value)
                                            <option value="{{ $role->value }}" selected>{{ $role->label() }}</option>
                                        @else
                                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="nik" class="col-sm-2 col-form-label">NIK *</label>
                            <div class="col-sm-10">
                                <input
                                class="form-control"
                                type="text"
                                maxlength="16"
                                id="nik"
                                name="nik"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                autofocus
                                required/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="no_kk" class="col-sm-2 col-form-label">Nomor KK *</label>
                            <div class="col-sm-10">
                                <input
                                class="form-control"
                                type="text"
                                maxlength="20"
                                id="no_kk"
                                name="no_kk"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                required/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="no_wa" class="col-sm-2 col-form-label">Nomor WA * </label>
                            <div class="col-sm-10">
                                <input
                                    class="form-control"
                                    type="tel"
                                    id="no_wa"
                                    name="no_wa"
                                    required
                                    />
                                    {{-- <small class="fst-italic text-danger">Pastikan nomor memiliki WA yang aktif, karena surat akan dikirim ke nomor ini</small> --}}
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="gender">Jenis Kelamin *</label>
                            <div class="col-sm-10">
                                <select name="gender" class="form-control" id="gender" required>
                                    @foreach ($arrJk as $jk)
                                        @if (old('gender') === $jk)
                                            <option value="{{ $jk }}" selected>{{ $jk }}</option>
                                        @else
                                            <option value="{{ $jk }}">{{ $jk }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="tempat_lhr" class="col-sm-2 col-form-label">Tempat / Tanggal Lahir *</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="tempat_lhr" id="tempat_lhr" placeholder="Birth Place" required />
                                    <span class="input-group-text" id="basic-addon13">/</span>
                                    <input type="date" class="form-control" name="tanggal_lhr" id="tanggal_lhr" placeholder="Birth Date" required />
                                </div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="agama" class="col-sm-2 col-form-label">Agama *</label>
                            <div class="col-sm-10">
                                <select name="agama" class="form-control" id="agama" required>
                                    @foreach ($agama as $item)
                                        @if (old('agama') === $item->value)
                                            <option value="{{ $item->value }}" selected>{{ $item->label() }}</option>
                                        @else
                                            <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="status_kawin" class="col-sm-2 col-form-label">Status Kawin *</label>
                            <div class="col-sm-10">
                                <select name="status_kawin" class="form-control" id="status_kawin" required>
                                    @foreach ($maritalStts as $item)
                                        @if (old('status_kawin') === $item->value)
                                            <option value="{{ $item->value }}" selected>{{ $item->label() }}</option>
                                        @else
                                            <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="pekerjaan" class="col-sm-2 col-form-label">Pekerjaan</label>
                            <div class="col-sm-10">
                            <input
                                class="form-control"
                                type="text"
                                id="pekerjaan"
                                name="pekerjaan"
                                />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="jabatan" class="col-sm-2 col-form-label">Jabatan</label>
                            <div class="col-sm-10">
                            <input
                                class="form-control"
                                type="text"
                                id="jabatan"
                                name="jabatan"
                                />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="tanggal_masuk" class="col-sm-2 col-form-label">Tanggal Masuk</label>
                            <div class="col-sm-10">
                            <input
                                class="form-control"
                                type="date"
                                id="tanggal_masuk"
                                name="tanggal_masuk"
                                />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="alamat_ktp" class="col-sm-2 col-form-label">Alamat KTP</label>
                            <div class="col-sm-10">
                                <textarea
                                    class="form-control"
                                    name="alamat_ktp"
                                    id="alamat_ktp"
                                    rows="2" placeholder="Alamat sesuai KTP"></textarea>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="alamat_dom" class="col-sm-2 col-form-label">Alamat Domisili</label>
                            <div class="col-sm-10">
                                <textarea
                                    class="form-control"
                                    name="alamat_dom"
                                    id="alamat_dom"
                                    rows="2" placeholder="Alamat sesuai Domisili"></textarea>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-sm-10 ms-auto">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active"/>
                                    <label class="form-check-label" for="is_active">Aktifkan akun</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

  <!-- Modal Edit -->
    <form id="formAuthenticationEdit" data-action="{{ route('master/aktor/pengguna.update', ':id') }}" method="POST">
      @csrf
      @method('PUT')
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true" data-bs-backdrop="static">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editModalLabel">Edit {{ $title ?? '-' }}</h5>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="name">Nama *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" placeholder="Enter your name" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="username">Username *</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="username" name="username" value="{{ old('username') }}" placeholder="Enter your username" />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="email">Email *</label>
                            <div class="col-sm-10">
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Enter your email" />
                            </div>
                        </div>
                        <div class="row mb-6 form-password-toogle">
                            <label class="col-sm-2 col-form-label" for="password">Password *</label>
                            <div class="col-sm-10">
                                <div class="input-group input-group-merge">
                                    <input
                                    type="password"
                                    class="form-control password-field"
                                    name="password"
                                    placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                    aria-describedby="password"
                                    />
                                    <span class="input-group-text cursor-pointer toggle-password"><i class="icon-base bx bx-hide"></i></span>
                                </div>
                                <div class="text-danger small fst-italic">Kosongkan jika tidak ingin mengubah password</div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="role">Role *</label>
                            <div class="col-sm-10">
                                <select name="role" class="form-control" id="role" required>
                                    @foreach ($roles as $role)
                                        @if (old('role') === $role->value)
                                            <option value="{{ $role->value }}" selected>{{ $role->label() }}</option>
                                        @else
                                            <option value="{{ $role->value }}">{{ $role->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="nik" class="col-sm-2 col-form-label">NIK *</label>
                            <div class="col-sm-10">
                                <input
                                class="form-control"
                                type="text"
                                maxlength="16"
                                id="nik"
                                name="nik"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                autofocus
                                required/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="no_kk" class="col-sm-2 col-form-label">Nomor KK *</label>
                            <div class="col-sm-10">
                                <input
                                class="form-control"
                                type="text"
                                maxlength="20"
                                id="no_kk"
                                name="no_kk"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '');"
                                required/>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="no_wa" class="col-sm-2 col-form-label">Nomor WA * </label>
                            <div class="col-sm-10">
                                <input
                                    class="form-control"
                                    type="tel"
                                    id="no_wa"
                                    name="no_wa"
                                    required
                                    />
                                    {{-- <small class="fst-italic text-danger">Pastikan nomor memiliki WA yang aktif, karena surat akan dikirim ke nomor ini</small> --}}
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label class="col-sm-2 col-form-label" for="gender">Jenis Kelamin *</label>
                            <div class="col-sm-10">
                                <select name="gender" class="form-control" id="gender" required>
                                    @foreach ($arrJk as $jk)
                                        @if (old('gender') === $jk)
                                            <option value="{{ $jk }}" selected>{{ $jk }}</option>
                                        @else
                                            <option value="{{ $jk }}">{{ $jk }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="tempat_lhr" class="col-sm-2 col-form-label">Tempat / Tanggal Lahir *</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="text" class="form-control" name="tempat_lhr" id="tempat_lhr" placeholder="Birth Place" required />
                                    <span class="input-group-text" id="basic-addon13">/</span>
                                    <input type="date" class="form-control" name="tanggal_lhr" id="tanggal_lhr" placeholder="Birth Date" required />
                                </div>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="agama" class="col-sm-2 col-form-label">Agama *</label>
                            <div class="col-sm-10">
                                <select name="agama" class="form-control" id="agama" required>
                                    @foreach ($agama as $item)
                                        @if (old('agama') === $item->value)
                                            <option value="{{ $item->value }}" selected>{{ $item->label() }}</option>
                                        @else
                                            <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="status_kawin" class="col-sm-2 col-form-label">Status Kawin *</label>
                            <div class="col-sm-10">
                                <select name="status_kawin" class="form-control" id="status_kawin" required>
                                    @foreach ($maritalStts as $item)
                                        @if (old('status_kawin') === $item->value)
                                            <option value="{{ $item->value }}" selected>{{ $item->label() }}</option>
                                        @else
                                            <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="pekerjaan" class="col-sm-2 col-form-label">Pekerjaan</label>
                            <div class="col-sm-10">
                            <input
                                class="form-control"
                                type="text"
                                id="pekerjaan"
                                name="pekerjaan"
                                />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="jabatan" class="col-sm-2 col-form-label">Jabatan</label>
                            <div class="col-sm-10">
                            <input
                                class="form-control"
                                type="text"
                                id="jabatan"
                                name="jabatan"
                                />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="tanggal_masuk" class="col-sm-2 col-form-label">Tanggal Masuk</label>
                            <div class="col-sm-10">
                            <input
                                class="form-control"
                                type="date"
                                id="tanggal_masuk"
                                name="tanggal_masuk"
                                />
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="alamat_ktp" class="col-sm-2 col-form-label">Alamat KTP</label>
                            <div class="col-sm-10">
                                <textarea
                                    class="form-control"
                                    name="alamat_ktp"
                                    id="alamat_ktp"
                                    rows="2" placeholder="Alamat sesuai KTP"></textarea>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <label for="alamat_dom" class="col-sm-2 col-form-label">Alamat Domisili</label>
                            <div class="col-sm-10">
                                <textarea
                                    class="form-control"
                                    name="alamat_dom"
                                    id="alamat_dom"
                                    rows="2" placeholder="Alamat sesuai Domisili"></textarea>
                            </div>
                        </div>
                        <div class="row mb-6">
                            <div class="col-sm-10 ms-auto">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_active" id="is_active"/>
                                    <label class="form-check-label" for="is_active">Aktifkan akun</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </div>
            </div>
        </div>
    </form>


    @push('page-js')
    <script>
        var table;
        var selectedRole = "";

        $(document).ready(function(){
            table = $('#tabel-data').DataTable({
                processing:true,
                serverSide:true,
                ajax:{
                    url:"{{ route('user.getData') }}",
                    data: function(d){
                        d.role_name = selectedRole;
                    }
                },
                columns:[
                    {
                        data:'action',
                        name:'action',
                        orderable:false,
                        searchable:false
                    },
                    {
                        data:'name',
                        name:'name'
                    },
                    {
                        data:'username',
                        name:'username'
                    },
                    {
                        data:'email',
                        name:'email'
                    },
                    {
                        data:'gender',
                        name:'gender'
                    },
                    {
                        data:'no_wa',
                        name:'no_wa'
                    },
                    {
                        data:'nik',
                        name:'nik'
                    },
                    {
                        data:'ttl',
                        name:'ttl'
                    },
                    {
                        data:'status_kawin',
                        name:'status_kawin'
                    },
                    {
                        data:'role',
                        name:'role'
                    },
                    {
                        data:'is_active',
                        name:'is_active'
                    }
                ]
            });

            $('#roleTabs button').on('click', function(){
                var tabId = $(this).attr('id');
                if (tabId === 'tab-all') {
                    selectedRole = "";
                }else{
                    selectedRole = tabId.replace('tab-', '');
                }
                table.ajax.reload();
            });
        });
    </script>

    {{-- konfirmasi modal delete --}}
        <script>
            var formToSubmit;
            function confirmDelete(id){
                formToSubmit = document.getElementById('deleteForm-' + id);
                var confirmModal = new bootstrap.Modal(document.getElementById('confirmModal'));
                confirmModal.show();
            }

            document.addEventListener('DOMContentLoaded', function(){
                var confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
                if (confirmDeleteBtn) {
                    confirmDeleteBtn.addEventListener('click', function(){
                        if (formToSubmit) {
                            formToSubmit.submit();
                        }
                    });
                }
            });
        </script>

        {{-- open modal edit --}}
        <script>
            function openModalCreate(){
                const modal = new bootstrap.Modal(document.getElementById('createModal'));
                modal.show();
            }
            function openModalEdit(idUser){
                var form = document.getElementById('formAuthenticationEdit');
                var editModal = form.querySelector('#editModal');
                var actionTemplate = form.getAttribute('data-action');

                $.ajax({
                    url:"{{ route('user.getDetail') }}",
                    type:'post',
                    data:{
                        _token:"{{ csrf_token() }}",
                        user_id: idUser
                    },
                    success:function(res){
                        if (res.status) {
                            var item = res.data;

                            form.action = actionTemplate.replace(':id', item.id);
                            editModal.querySelector('#name').value = item.name;
                            editModal.querySelector('#username').value = item.username;
                            editModal.querySelector('#email').value = item.email;
                            editModal.querySelector('#role').value = item.role;
                            editModal.querySelector('#nik').value = item.nik;
                            editModal.querySelector('#no_kk').value = item.no_kk;
                            editModal.querySelector('#no_wa').value = item.no_wa;
                            editModal.querySelector('#gender').value = item.gender;
                            editModal.querySelector('#tempat_lhr').value = item.tempat_lhr;
                            editModal.querySelector('#tanggal_lhr').value = item.tanggal_lhr;
                            editModal.querySelector('#agama').value = item.agama;
                            editModal.querySelector('#status_kawin').value = item.status_kawin;
                            editModal.querySelector('#pekerjaan').value = item.pekerjaan;
                            editModal.querySelector('#jabatan').value = item.jabatan;
                            editModal.querySelector('#tanggal_masuk').value = item.tanggal_masuk;
                            editModal.querySelector('#alamat_ktp').value = item.alamat_ktp;
                            editModal.querySelector('#alamat_dom').value = item.alamat_dom;
                            editModal.querySelector('#is_active').checked = item.is_active;

                            var modalEdit = new bootstrap.Modal(editModal);
                            modalEdit.show();
                        }else{
                            Toast.fire({
                                icon:'error',
                                text:res.message
                            });
                        }
                    },
                    error:function(xhr){
                        Toast.fire({
                            icon:'error',
                            text:xhr.responseText
                        });
                    }
                });
            }

            $(document).on('click', '.toggle-password', function () {
                const input = $(this).siblings('input.password-field');
                const icon = $(this).find('i');

                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    icon.removeClass('bx-hide').addClass('bx-show');
                } else {
                    input.attr('type', 'password');
                    icon.removeClass('bx-show').addClass('bx-hide');
                }
            });

        </script>
    @endpush
@endsection
