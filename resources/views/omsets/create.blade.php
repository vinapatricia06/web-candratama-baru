@extends('layouts.admin.app')

@section('title', 'Tambah Omset')

@section('content')
    <div class="container">
        <h2>Tambah Omset</h2>

        @if ($errors->has('klien_id'))
            <script>
                alert('Klien yang dipilih tidak valid.');
            </script>
        @endif

        @if ($errors->has('no_induk'))
            <script>
                alert('No Induk yang dimasukkan sudah ada. Silakan pilih No Induk yang lain.');
            </script>
        @endif


        <form action="{{ route('omsets.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label>Tanggal</label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="klien_id">Nama Klien</label>
                <select id="klien_id" class="form-control" required>
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($kliens as $item)
                        <option value="{{ $item->klien_id }}" data-no_induk="{{ $item->klien->no_induk }}"
                            data-alamat="{{ $item->klien->alamat }}">
                            {{ $item->klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
                @error('klien_id')
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label>No Induk</label>
                <input type="text" id="no_induk" class="form-control" readonly required>
            </div>

            <div class="mb-3">
                <label>Alamat</label>
                <textarea id="alamat" class="form-control" rows="3" readonly required></textarea>
            </div>

            <div class="mb-3">
                <label>Project</label>
                <select id="progress_projects_id" name="progress_projects_id" class="form-control" required>
                    <option value="">-- Pilih Project --</option>
                </select>
            </div>

            <div class="form-group">
                <label for="sumber_lead">Sumber Lead</label>
                <input type="text" name="sumber_lead" id="sumber_lead" class="form-control"
                    value="{{ old('sumber_lead') }}">
            </div>

            <div class="form-group">
                <label for="nominal">Nominal</label>
                <input type="number" name="nominal" id="nominal" class="form-control" required readonly>
            </div>
            <br>
            <a href="{{ route('omsets.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection
@section('script')
    <script>
        document.getElementById('klien_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const noInduk = selected.getAttribute('data-no_induk');
            const alamat = selected.getAttribute('data-alamat');

            document.getElementById('no_induk').value = noInduk || '';
            document.getElementById('alamat').value = alamat || '';

            var klien_id = $('#klien_id').val();

            $.ajax({
                url: "{{ route('get_data_project') }}",
                type: 'GET',
                data: {
                    klien_id: klien_id
                },
                beforeSend: function() {
                    $('#progress_projects_id').empty();
                    $('#progress_projects_id').append('<option value="">-- Loading Data --</option>');
                },
                success: function(response) {
                    $('#progress_projects_id').empty();
                    $('#progress_projects_id').append('<option value="">-- Pilih Project --</option>');

                    $.each(response, function(index, project) {
                        $('#progress_projects_id').append(
                            $('<option>', {
                                value: project.id,
                                text: project.project,
                                'data-nominal': project.nominal
                            })
                        );
                    });
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                }
            });
        });

        document.getElementById('progress_projects_id').addEventListener('change', function() {
            const selected = this.options[this.selectedIndex];
            const nominal = selected.getAttribute('data-nominal');

            document.getElementById('nominal').value = nominal || '';
        });
    </script>
@endsection
