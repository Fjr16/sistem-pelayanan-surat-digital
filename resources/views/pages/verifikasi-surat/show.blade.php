<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Verifikasi TTD</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
  <div class="row justify-content-center">
    <div class="col-md-8">

      <div class="card shadow-sm p-4">
        <h3 class="mb-3 text-center">🔐 Hasil Verifikasi QR</h3>

        {{-- Pesan verifikasi --}}
        @if($getMessage === 'Surat Dinyatakan VALID (SAH)')
          <div class="alert alert-success text-center fw-bold">{{ $getMessage }}</div>
        @elseif($getMessage === 'Surat Tidak Terdaftar')
          <div class="alert alert-warning text-center fw-bold">{{ $getMessage }}</div>
        @else
          <div class="alert alert-danger text-center fw-bold">{{ $getMessage }}</div>
        @endif

        {{-- Detail Surat jika valid --}}
        @if($getMessage === 'Surat Dinyatakan VALID (SAH)' && $surat)
          <div class="mt-4">
            <h5 class="mb-3">📄 Detail Surat</h5>
            <table class="table table-bordered">
              <tr>
                <th width="30%">Nomor Surat</th>
                <td>{{ $surat->letter_number }}</td>
              </tr>
              <tr>
                <th>Dibuat Pada</th>
                <td>{{ \Carbon\Carbon::parse($surat->created_at)->format('d-m-Y') }}</td>
              </tr>
              <tr>
                <th>Disahkan Pada</th>
                <td>{{ \Carbon\Carbon::parse($surat->updated_at)->format('d-m-Y') }}</td>
              </tr>
              <tr>
                <th>Nama Pemohon</th>
                <td>{{ $surat->penduduk->name ?? '-' }}</td>
              </tr>
              <tr>
                <th>NIK Pemohon</th>
                <td>{{ $surat->penduduk->nik ?? '-' }}</td>
              </tr>
              <tr>
                <th>Perihal</th>
                <td>{{ $surat->mail->name }}</td>
              </tr>
            </table>
          </div>
        @endif

        <div class="text-center mt-4">
          <a href="/" class="btn btn-secondary">Kembali ke Beranda</a>
        </div>

      </div>

    </div>
  </div>
</div>

</body>
</html>
