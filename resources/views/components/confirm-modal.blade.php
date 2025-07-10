{{-- modal confirm delete --}}
<div class="modal fade" id="confirmModal" tabindex="-1" aria-labelledby="confirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="confirmModalLabel">{{ $title ?? 'Konfirmasi Hapus' }} </h5>
        </div>
        <div class="modal-body">
            {{ $message ?? 'Yakin ingin melakukan aksi ini ?' }}
        </div>
        <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        <button type="button" class="btn btn-danger" id="confirmDeleteBtn">Hapus</button>
        </div>
    </div>
    </div>
</div>