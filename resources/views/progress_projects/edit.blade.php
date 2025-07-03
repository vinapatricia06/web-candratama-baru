@extends('layouts.admin.app')

@section('title', 'Edit Progress Project')

@section('content')
    <div class="container">
        <h2>Edit Project</h2>

        <!-- Error Messages -->
        @if ($errors->has('dokumentasi'))
            <script>
                alert('Ukuran gambar yang diunggah melebihi batas maksimum 1.5MB. Silakan kompres gambar terlebih dahulu.');
            </script>
        @endif

        <form action="{{ route('progress_projects.update', $progress_project->id) }}" method="POST"
            enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <!-- Teknisi Dropdown -->
            <div class="mb-3">
                <label>Teknisi</label>
                <select name="teknisi_id" class="form-control" required>
                    <option value="">-- Pilih Teknisi --</option>
                    @foreach ($teknisiList as $teknisi)
                        <option value="{{ $teknisi->id_user }}"
                            {{ $progress_project->teknisi_id == $teknisi->id_user ? 'selected' : '' }}>
                            {{ $teknisi->nama }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Klien Dropdown -->
            <div class="mb-3">
                <label>Nama Klien</label>
                <select name="klien_id" id="klien_id" class="form-control" required>
                    <option value="">-- Pilih Klien --</option>
                    @foreach ($kliens as $klien)
                        <option value="{{ $klien->id }}" data-no_induk="{{ $klien->no_induk }}"
                            data-alamat="{{ $klien->alamat }}"
                            {{ $progress_project->klien_id == $klien->id ? 'selected' : '' }}>
                            {{ $klien->nama_klien }}
                        </option>
                    @endforeach
                </select>
            </div>
            <!-- Alamat -->
            <div class="mb-3">
                <label>Alamat</label>
                <textarea id="alamat" class="form-control" required readonly>{{ $progress_project->klien->alamat }}</textarea>
            </div>

            <!-- Project -->
            <div class="mb-3">
                <label>Project</label>
                <input type="text" name="project" class="form-control"
                    value="{{ old('project', $progress_project->project) }}" required>
            </div>

            <!-- Tanggal Mulai -->
            <div class="mb-3">
                <label>Tanggal Mulai</label>
                <input type="date" name="tanggal_mulai" class="form-control"
                    value="{{ old('tanggal_mulai', $progress_project->tanggal_mulai) }}" required>
            </div>

            <!-- Tanggal Selesai -->
            <div class="mb-3">
                <label>Tanggal Selesai</label>
                <input type="date" name="tanggal_selesai" class="form-control"
                    value="{{ old('tanggal_selesai', $progress_project->tanggal_selesai) }}" required>
            </div>

            <!-- Dokumentasi -->
            <div class="mb-3">
                <label>Dokumentasi</label>
                <br>
                @if ($progress_project->dokumentasi)
                    <img src="{{ asset($progress_project->dokumentasi) }}" alt="Dokumentasi" width="150">
                @else
                    Tidak ada gambar
                @endif
                <br><br>
                <input type="file" name="dokumentasi" class="form-control">
            </div>

            <!-- Status -->
            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control" required>
                    <option value="">-- Pilih Status --</option>
                    <option value="Inisialisasi" {{ $progress_project->status == 'Inisialisasi' ? 'selected' : '' }}>
                        Inisialisasi</option>
                    <option value="Diproses" {{ $progress_project->status == 'Diproses' ? 'selected' : '' }}>Diproses
                    </option>
                    <option value="Dibatalkan" {{ $progress_project->status == 'Dibatalkan' ? 'selected' : '' }}>
                        Dibatalkan
                    <option value="Selesai" {{ $progress_project->status == 'Selesai' ? 'selected' : '' }}>Selesai
                    </option>
                </select>
            </div>

            <!-- Nominal -->
            <div class="mb-3">
                <label>Nominal</label>
                <input type="number" name="nominal" class="form-control"
                    value="{{ old('nominal', $progress_project->nominal) }}" required>
            </div>

            <div class="mb-3">
                <label>Metode Pembayaran</label>
                <select name="is_hutang" id="metode_pembayaran" class="form-control" required
                    @if (in_array($progress_project->status_pembayaran, ['Sudah Dibayar', 'Dibayar Sebagian', 'Lunas'])) disabled @endif>
                    <option value="">-- Pilih Metode --</option>
                    <option value="0" {{ $progress_project->is_hutang == 0 ? 'selected' : '' }}>Pembayaran Langsung</option>
                    <option value="1" {{ $progress_project->is_hutang == 1 ? 'selected' : '' }}>Hutang</option>
                </select>
            </div>

            <div id="form_angsuran" style="{{ $progress_project->is_hutang == 1 ? '' : 'display: none;' }}">
                <div class="mb-3">
                    <label>Uang Muka</label>
                    <input type="number" name="uang_muka" id="uang_muka" class="form-control" min="1"
                        placeholder="Masukkan uang muka" value="{{ old('nominal', $progress_project->uang_muka) }}"
                        @if (in_array($progress_project->status_pembayaran, ['Dibayar Sebagian', 'Lunas'])) disabled @endif>
                </div>

                <div class="mb-3">
                    <label>Tanggal Awal Angsuran</label>
                    <input type="date" name="tanggal_awal_angsuran" id="tanggal_awal_angsuran" class="form-control"
                        value="{{ optional($progress_project->debtPayments->first())->tanggal_angsuran }}"
                        @if (in_array($progress_project->status_pembayaran, ['Dibayar Sebagian', 'Lunas'])) disabled @endif>
                </div>

                <div class="mb-3">
                    <label>Jumlah Angsuran (Berapa Kali)</label>
                    <input type="number" name="jumlah_angsuran" id="jumlah_angsuran" class="form-control"
                        placeholder="Masukkan jumlah angsuran"
                        value="{{ old('jumlah_angsuran', $progress_project->debtPayments ? $progress_project->debtPayments->count() : '') }}"
                        @if (in_array($progress_project->status_pembayaran, ['Dibayar Sebagian', 'Lunas'])) disabled @endif>
                </div>

                <div class="mb-3">
                    <div class="alert alert-info" id="keterangan_angsuran" style="display: none;">
                        <h6><strong>Keterangan Angsuran:</strong></h6>
                        <p id="detail_angsuran"></p>
                    </div>
                </div>
            </div>

            <a href="{{ route('progress_projects.index') }}" class="btn btn-danger mr-2">Kembali</a>
            <button type="submit" class="btn btn-success">Update</button><br><br>
        </form>
    </div>
@endsection
@section('script')
    <script>
        // Ensure the script runs after DOM content is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Isi otomatis No Induk dan Alamat berdasarkan pilihan Nama Klien
            document.getElementById('klien_id').addEventListener('change', function() {
                const selected = this.options[this.selectedIndex];
                const alamat = selected.getAttribute('data-alamat');

                document.getElementById('alamat').value = alamat || '';
            });
        });
        document.addEventListener('DOMContentLoaded', function() {
            const tanggalMulai = document.querySelector('input[name="tanggal_mulai"]');
            const tanggalSelesai = document.querySelector('input[name="tanggal_selesai"]');

            tanggalMulai.addEventListener('change', function() {
                tanggalSelesai.min = this.value;

                if (tanggalSelesai.value < this.value) {
                    tanggalSelesai.value = '';
                }
            });

            const metodePembayaran = document.getElementById('metode_pembayaran');
            const formAngsuran = document.getElementById('form_angsuran');
            const tanggalAwalAngsuran = document.getElementById('tanggal_awal_angsuran');
            const jumlahAngsuran = document.getElementById('jumlah_angsuran');
            const nominal = document.querySelector('input[name="nominal"]');
            const uang_muka = document.querySelector('input[name="uang_muka"]');
            const keteranganAngsuran = document.getElementById('keterangan_angsuran');
            const detailAngsuran = document.getElementById('detail_angsuran');

            function formatRupiah(angka) {
                return new Intl.NumberFormat('id-ID', {
                    style: 'currency',
                    currency: 'IDR',
                    minimumFractionDigits: 0
                }).format(angka);
            }

            function formatDate(dateString) {
                const date = new Date(dateString);
                return date.toLocaleDateString('id-ID', {
                    day: '2-digit',
                    month: '2-digit',
                    year: 'numeric'
                });
            }

            function calculateAngsuran() {
                const nominalValue = parseInt(nominal.value) || 0;
                const dpValue = parseInt(uang_muka.value) || 0;
                const totalValue = nominalValue - dpValue;
                const jumlahAngsuranValue = parseInt(jumlahAngsuran.value) || 0;
                const tanggalAwal = tanggalAwalAngsuran.value;

                if (dpValue >= nominalValue && nominalValue > 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Uang Muka Tidak Valid',
                        text: 'Uang muka tidak boleh lebih besar atau sama dengan nominal.',
                        confirmButtonText: 'OK'
                    });
                    uang_muka.value = '';
                    uang_muka.focus();
                    keteranganAngsuran.style.display = 'none';
                    return;
                }

                if (totalValue > 0 && jumlahAngsuranValue > 0 && tanggalAwal) {
                    const nominalPerAngsuran = Math.ceil(totalValue / jumlahAngsuranValue);

                    const startDate = new Date(tanggalAwal);
                    const endDate = new Date(startDate);
                    endDate.setMonth(endDate.getMonth() + jumlahAngsuranValue - 1);

                    const detailText =
                        `Pembayaran angsuran sebesar ${formatRupiah(nominalPerAngsuran)} mulai ${formatDate(tanggalAwal)} sampai ${formatDate(endDate.toISOString().split('T')[0])}`;

                    detailAngsuran.textContent = detailText;
                    keteranganAngsuran.style.display = 'block';
                } else {
                    keteranganAngsuran.style.display = 'none';
                }
            }

            metodePembayaran.addEventListener('change', function() {
                if (this.value === '1') {
                    formAngsuran.style.display = 'block';
                    tanggalAwalAngsuran.required = true;
                    jumlahAngsuran.required = true;
                } else {
                    formAngsuran.style.display = 'none';
                    tanggalAwalAngsuran.required = false;
                    jumlahAngsuran.required = false;
                    tanggalAwalAngsuran.value = '';
                    jumlahAngsuran.value = '';
                    keteranganAngsuran.style.display = 'none';
                }
            });

            nominal.addEventListener('input', calculateAngsuran);
            uang_muka.addEventListener('input', calculateAngsuran);
            jumlahAngsuran.addEventListener('input', calculateAngsuran);
            tanggalAwalAngsuran.addEventListener('change', calculateAngsuran);
            if (metodePembayaran.value === '1') {
                calculateAngsuran();
            }
        });
    </script>
@endsection
