@extends('layouts.admin.app')

@section('title', 'Edit Omset')

@section('content')
    <div class="container">
        <h2>Edit Omset</h2>

        <!-- Menampilkan pesan error jika ada -->
        @if ($errors->has('klien_id'))
            <script>
                alert('Klien yang dipilih tidak valid.');
            </script>
        @endif

        <form action="{{ route('omsets.update', $omset->id_omset) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Tanggal -->
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" value="{{ $omset->tanggal }}" required>
            </div>

            <!-- Nama Klien (Jika tidak ingin mengubah klien, tidak perlu memilih) -->
            <div class="form-group">
                <label for="klien_id">Nama Klien</label>
                <select name="klien_id" id="klien_id" class="form-control" required>
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($kliens as $item)
                        <option value="{{ $item->klien_id }}" data-no_induk="{{ $item->klien->no_induk }}"
                            data-alamat="{{ $item->klien->alamat }}"
                            {{ $omset->project->klien_id == $item->klien_id ? 'selected' : '' }}>
                            {{ $item->klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
                @error('klien_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <!-- No Induk -->
            <div class="mb-3">
                <label>No Induk</label>
                <input type="text" id="no_induk" class="form-control" value="{{ $omset->project->klien->no_induk }}"
                    required readonly>
            </div>

            <!-- Alamat -->
            <div class="mb-3">
                <label>Alamat</label>
                <textarea id="alamat" class="form-control" required readonly>{{ $omset->project->klien->alamat }}</textarea>
            </div>

            <!-- Project -->
            <div class="mb-3">
                <label>Project</label>
                <select id="progress_projects_id" name="progress_projects_id" class="form-control" required>
                    <option value="">-- Pilih Project --</option>
                </select>
            </div>

            <!-- Sumber Lead -->
            <div class="form-group">
                <label for="sumber_lead">Sumber Lead</label>
                <input type="text" name="sumber_lead" id="sumber_lead" class="form-control"
                    value="{{ old('sumber_lead', $omset->sumber_lead) }}">
            </div>

            <!-- Nominal -->
            <div class="mb-3">
                <label>Nominal</label>
                <input type="number" name="nominal" id="nominal" class="form-control"
                    value="{{ old('nominal', intval($omset->nominal)) }}" required readonly>
            </div>

            <div class="mb-3">
                <label>Metode Pembayaran</label>
                <select name="metode_pembayaran" class="form-control" required>
                    <option value="tunai" {{ $omset->metode_pembayaran == 'tunai' ? 'selected' : '' }}>Tunai/Cash</option>
                    <option value="midtrans" {{ $omset->metode_pembayaran == 'midtrans' ? 'selected' : '' }}>Midtrans
                    </option>
                </select>
            </div>

            <div class="form-group">
                <label>Pembayaran</label>
                <select name="catatan_pembayaran" class="form-control" required>
                    <option value="PROJECT" {{ $omset->catatan_pembayaran == 'PROJECT' ? 'selected' : '' }}>Pembayaran
                        Project</option>
                    <option value="MAINTENANCE" {{ $omset->catatan_pembayaran == 'MAINTENANCE' ? 'selected' : '' }}>
                        Pembayaran Biaya Tambahan Maintenance</option>
                </select>
            </div>

            <a href="{{ route('omsets.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Update</button>
        </form>
    </div>
@endsection
@section('script')
    <script>
        var project_id = "{{ $omset->progress_projects_id }}"
        $(document).ready(function() {
            get_data_project(project_id);
        });

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('klien_id').addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const noInduk = selected.getAttribute('data-no_induk');
                const alamat = selected.getAttribute('data-alamat');

                document.getElementById('no_induk').value = noInduk || '';
                document.getElementById('alamat').value = alamat || '';

                get_data_project();
            });
        });

        document.getElementById('progress_projects_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const nominal = selected.getAttribute('data-nominal');

            document.getElementById('nominal').value = nominal || '';
        });

        function get_data_project(selected = null) {
            var klien_id = $('#klien_id').val();

            $.ajax({
                url: "{{ route('get_data_project') }}",
                type: 'GET',
                data: {
                    klien_id: klien_id
                },
                beforeSend: function() {
                    $('#progress_projects_id').empty();
                    $('#progress_projects_id').append(
                        '<option value="">-- Loading Data --</option>');
                },
                success: function(response) {
                    $('#progress_projects_id').empty();
                    $('#progress_projects_id').append(
                        '<option value="">-- Pilih Project --</option>');

                    $.each(response, function(index, project) {
                        $('#progress_projects_id').append(
                            $('<option>', {
                                value: project.id,
                                text: project.project,
                                'data-nominal': project.nominal
                            })
                        );
                    });
                    if (selected !== null) {
                        $('#progress_projects_id').val(selected).change();
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        }
    </script>
@endsection
