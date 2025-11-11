@extends('layout.auth.app')

@push('page-css')
<style>
    .auth-wide {
        max-width: 800px !important;
    }
</style>

@endpush
@section('loginContent')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner auth-wide">
      <div class="card px-sm-6 px-0">
        <div class="card-body">
          <h4 class="mb-1">{{ $title }} 👤</h4>
          <p class="mb-6">{{ $slug }}</p>

          <form id="formRegister" action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Step indicators -->
            <ul class="nav nav-pills mb-6 justify-content-center" id="registerSteps">
              <li class="nav-item">
                <button type="button" class="nav-link active" data-step="1" style="pointer-events: none;">Akun</button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" data-step="2" style="pointer-events: none;">Pribadi</button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" data-step="3" style="pointer-events: none;">Alamat</button>
              </li>
              <li class="nav-item">
                <button type="button" class="nav-link" data-step="4" style="pointer-events: none;">Lainnya</button>
              </li>
            </ul>

            <!-- Step 1 -->
            <div class="step d-none" data-step="1">
              <div class="mb-6">
                <label class="form-label">Username *</label>
                <input type="text" class="form-control" name="username" id="username" required value="{{ old('username') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">Password *</label>
                <input type="password" class="form-control" name="password" id="password" required value="{{ old('password') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">Konfirmasi Password *</label>
                <input type="password" class="form-control" id="password_confirm" required value="{{ old('password_confirm') }}">
                <div class="invalid-feedback">
                  <span class="text-danger">*Password tidak sama</span>
                </div>
              </div>
              <div class="mb-6">
                <label class="form-label">Email *</label>
                <input type="email" class="form-control" name="email" id="email" required value="{{ old('email') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">Nomor WhatsApp *</label>
                <input type="text" class="form-control" name="no_wa" required value="{{ old('no_wa') }}">
              </div>
            </div>

            <!-- Step 2 -->
            <div class="step d-none" data-step="2">
              <div class="mb-6">
                <label class="form-label">NIK *</label>
                <input type="text" class="form-control" name="nik" id="nik" required value="{{ old('nik') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">No KK *</label>
                <input type="text" class="form-control" name="no_kk" required value="{{ old('no_kk') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">Nama Lengkap *</label>
                <input type="text" class="form-control" name="name" required value="{{ old('name') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">Jenis Kelamin *</label>
                <select class="form-select" name="gender" required>
                  <option value="">-- Pilih --</option>
                  @foreach ($arrJk as $jk)
                    <option value="{{ $jk }}" @selected(old('gender') === $jk)>{{ $jk }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-6">
                <label class="form-label">Tanggal Lahir *</label>
                <input type="date" class="form-control" name="tanggal_lhr" required value="{{ old('tanggal_lhr') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">Tempat Lahir *</label>
                <input type="text" class="form-control" name="tempat_lhr" required value="{{ old('tempat_lhr') }}">
              </div>
            </div>

            <!-- Step 3 -->
            <div class="step d-none" data-step="3">
              <div class="mb-6">
                <label class="form-label">Alamat KTP</label>
                <textarea class="form-control" name="alamat_ktp">{{ old('alamat_ktp') }}</textarea>
              </div>
              <div class="mb-6">
                <label class="form-label">Alamat Domisili</label>
                <textarea class="form-control" name="alamat_dom">{{ old('alamat_dom') }}</textarea>
              </div>
            </div>

            <!-- Step 4 -->
            <div class="step d-none" data-step="4">
              <div class="mb-6">
                <label class="form-label">Agama *</label>
                <select class="form-select" name="agama" required>
                  <option value="">-- Pilih --</option>
                  @foreach ($agama as $item)
                    <option value="{{ $item->value }}" @selected(old('agama') === $item->value)>{{ $item->label() }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-6">
                <label class="form-label">Status Kawin *</label>
                <select class="form-select" name="status_kawin" required>
                  <option value="">-- Pilih --</option>
                  @foreach ($maritalStts as $item)
                    <option value="{{ $item->value }}" @selected(old('status_kawin') === $item->value)>{{ $item->label() }}</option>
                  @endforeach
                </select>
              </div>
              <div class="mb-6">
                <label class="form-label">Pekerjaan</label>
                <input type="text" class="form-control" name="pekerjaan" value="{{ old('pekerjaan') }}">
              </div>
              <div class="mb-6">
                <label class="form-label">Jabatan</label>
                <input type="text" class="form-control" name="jabatan" value="{{ old('jabatan') }}">
              </div>
            </div>

            <!-- Navigation buttons -->
            <div class="d-flex justify-content-between">
              <button type="button" class="btn btn-secondary" id="prevStep" disabled>Sebelumnya</button>
              <button type="button" class="btn btn-primary" id="nextStep">Berikutnya</button>
              <button type="submit" class="btn btn-success d-none" id="submitBtn">Daftar</button>
            </div>

          </form>
          <!-- Link ke login -->
          <hr>
          <p class="text-center mt-4 mb-0">
            <span>Sudah punya akun?</span>
            <a href="{{ route('login') }}" class="fw-bold">
              Login di sini
            </a>
          </p>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@push('page-js')
<script>
    let currentStep = 1;
    const totalSteps = document.querySelectorAll('.step').length;
    const showStep = (step) => {
        document.querySelectorAll('.step').forEach(el => {
            el.classList.toggle('d-none', el.dataset.step != step);
        });
        document.querySelectorAll('#registerSteps .nav-link').forEach(el => {
            el.classList.toggle('active', el.dataset.step == step);
        });

        document.getElementById('prevStep').disabled = step === 1;
        document.getElementById('nextStep').classList.toggle('d-none', step === totalSteps);
        document.getElementById('submitBtn').classList.toggle('d-none', step !== totalSteps);
    };

    showStep(currentStep);

    document.getElementById('nextStep').addEventListener('click', () => {
        const valid =  validateStep(currentStep);
        if (valid) {
            if (currentStep < totalSteps) currentStep++;
            showStep(currentStep);
        }
    });

    document.getElementById('prevStep').addEventListener('click', () => {
    if (currentStep > 1) currentStep--;
        showStep(currentStep);
    });

    document.getElementById('submitBtn').addEventListener('click', (e) => {
        e.preventDefault();
        const valid =  validateStep(currentStep);

        if (!valid) return;
        document.getElementById('formRegister').submit();
    });

    function validateStep(step) {
        // Cek: apakah ada minimal satu field yang masih is-invalid?
        let someFields = document.querySelectorAll('#username, #email, #nik, #password_confirm');
        const hasInvalid = [...someFields].some(field =>
            field.classList.contains('is-invalid')
        );

        if (hasInvalid) {
            // blok pindah step
            return;
        }

        let valid = true;
        let stepFields = document.querySelectorAll(`.step[data-step="${step}"] [required]`);

        stepFields.forEach(field => {
            if (!field.value.trim()) {
                field.classList.add("is-invalid");
                valid = false;
            } else {
                field.classList.remove("is-invalid");
            }
        });
        
        return valid;
    }
</script>
  
<script>
  const inputs = document.querySelectorAll('#username, #email, #nik');
  inputs.forEach(input => {
    input.addEventListener('input', async function(){
      let formData = new FormData();
      formData.append(this.name, this.value);

      try {
        const res = await fetch("{{ route('register.validation') }}", {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN' : "{{ csrf_token() }}",
            'Accept' : 'application/json'
          },
          body: formData
        });
  
        if (!res.ok) {
          throw new Error('Terjadi Kesalahan pada server');
        }
        const data = await res.json();

        if(data.status === false){
          const fieldErrors = data.errors[this.name] || [];
          if (fieldErrors.length > 0) {
            this.classList.add('is-invalid');

            let feedback = this.nextElementSibling;
            if (!feedback || !feedback.classList.contains('invalid-feedback')) {
              feedback = document.createElement('div');
              feedback.className = 'invalid-feedback';
              this.insertAdjacentElement('afterend', feedback);
            }
            feedback.textContent = fieldErrors[0];
          }
        }else{
          this.classList.remove('is-invalid');

          let feedback = this.nextElementSibling;
          if (feedback && feedback.classList.contains('invalid-feedback')) {
            feedback.textContent = '';
          }
        }

      } catch (error) {
        Toast.fire({
          icon:'error',
          text:error.message
        });
        console.log(error.message);
      }
    });
  });

  const pwConfirm = document.querySelectorAll('#password_confirm, #password');
  pwConfirm.forEach(pw => {
    pw.addEventListener('input', function(){
      let pass = document.querySelector("#password");
      let confirm = document.querySelector("#password_confirm"); 
      if (pass.value !== confirm.value) {
        confirm.classList.add("is-invalid");
      }else{
        confirm.classList.remove("is-invalid");
      }
    });
  })
</script>
@endpush

