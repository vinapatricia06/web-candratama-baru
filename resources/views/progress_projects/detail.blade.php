@extends('layouts.admin.app')

@section('title', 'Detail Pembayaran Project')

@section('content')
    <div id="loading-overlay"
        style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.7); z-index: 9999; display: flex; justify-content: center; align-items: center;">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">Memproses Data...</span>
        </div>
    </div>
    <div class="container-fluid">
        <h2>Daftar Angsuran Project</h2>
    </div>
    <div class="table-responsive" style="max-width: 100%; overflow-x: auto;">
        <table class="table table-bordered" style="font-size: 18px; width: 100%; table-layout: auto;">
            <thead class="table-light">
                <tr>
                    <th>Angsuran Ke</th>
                    <th>Tanggal Angsuran</th>
                    <th>Nominal</th>
                    <th>Status Pembayaran</th>
                    <th>Tanggal Pembayaran</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($data as $key => $item)
                    <tr>
                        <td>{{ $key + 1 }}</td>
                        <td>{{ \Carbon\Carbon::parse($item->tanggal_angsuran)->translatedFormat('d F Y') }}</td>
                        <td>{{ 'Rp ' . number_format($item->nominal, 0, ',', '.') }}</td>
                        <td>{{ $item->status_pembayaran }}</td>
                        <td>
                            @if ($item->tanggal_pembayaran)
                                {{ \Carbon\Carbon::parse($item->tanggal_pembayaran)->translatedFormat('d F Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @if (
                                (auth()->check() && auth()->user()->hasRole('superadmin')) ||
                                    auth()->user()->hasRole('finance') ||
                                    auth()->user()->hasRole('admin'))
                                @if ($item->status_pembayaran == 'Belum Dibayar')
                                    <button class="btn btn-dark btn-sm" onclick="pembayaran({{ $item->id }})">
                                        <i class="fas fa-money-bill-wave"></i> Pembayaran
                                    </button>
                                    <a href="{{ route('progress_projects.invoiceDebt', $item->id) }}" target="_blank"
                                        class="btn btn-info btn-sm">
                                        <i class="fas fa-file-invoice"></i> Cetak Invoice
                                    </a>
                                @elseif($item->status_pembayaran == 'Sudah Dibayar')
                                    <a href="{{ route('progress_projects.nota_hutang', $item->id) }}" target="_blank"
                                        class="btn btn-dark btn-sm">
                                        <i class="fas fa-print"></i> Cetak Nota
                                    </a>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada data yang ditemukan</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@section('script')
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-3exfNPbeb-yYb9s7"></script>
    <script>
        $(document).ready(function() {
            $('#loading-overlay').fadeOut();
        });

        function pembayaran(debt_payment_id) {
            Swal.fire({
                title: 'Masukkan Informasi Pembayaran',
                html: '<label for="sumberLead">Sumber Lead</label>' +
                    '<input id="sumberLead" class="swal2-input" placeholder="Contoh: Instagram, Website, Referral, dll">' +
                    '<label for="metodePembayaran">Metode Pembayaran</label>' +
                    '<select id="metodePembayaran" class="swal2-select">' +
                    '<option value="">-- Pilih Metode Pembayaran --</option>' +
                    '<option value="midtrans">Midtrans</option>' +
                    '<option value="tunai">Tunai</option>' +
                    '</select>',
                showCancelButton: true,
                confirmButtonText: 'Lanjutkan Pembayaran',
                cancelButtonText: 'Batal',
                preConfirm: () => {
                    const sumberLead = document.getElementById('sumberLead').value;
                    const metode = document.getElementById('metodePembayaran').value;

                    if (!sumberLead || !metode) {
                        Swal.showValidationMessage('Sumber Lead dan Metode Pembayaran wajib diisi!');
                        return false;
                    }

                    return {
                        sumber_lead: sumberLead,
                        metode_pembayaran: metode
                    };
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const {
                        sumber_lead,
                        metode_pembayaran
                    } = result.value;

                    var formData = new FormData();
                    formData.append('debt_payment_id', debt_payment_id);
                    formData.append('sumber_lead', sumber_lead);
                    formData.append('metode_pembayaran', metode_pembayaran);

                    $.ajax({
                        url: "{{ route('createTransactionDebt') }}",
                        type: 'post',
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: () => {
                            $('#loading-overlay').fadeIn();
                        },
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: (response) => {
                            $('#loading-overlay').fadeOut();
                            if (response.status === 'success') {
                                if (response.metode == 'midtrans') {
                                    payWithMidtrans(response.snap_token, response.order_id,
                                        response
                                        .sumber_lead, metode_pembayaran);
                                } else {
                                    Notiflix.Notify.success('Pembayaran Berhasil');
                                    const finishRedirectUrl = '/suksesDebt';
                                    window.location.href =
                                        `${finishRedirectUrl}/${response.order_id}/${response.sumber_lead}/${metode_pembayaran}`;
                                }
                            } else {
                                Notiflix.Notify.failure(response.message || 'Transaksi gagal.');
                            }
                        },
                        error: (xhr) => {
                            $('#loading-overlay').fadeOut();
                            let message = 'Terjadi kesalahan saat membuat transaksi.';
                            if (xhr.responseJSON && xhr.responseJSON.message) {
                                message = xhr.responseJSON.message;
                            }
                            Notiflix.Notify.failure(message);
                        }
                    });
                }
            });
        }

        function payWithMidtrans(snapToken, order_id, sumber_lead, metode_pembayaran) {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    Notiflix.Notify.success('Pembayaran Berhasil');
                    const finishRedirectUrl = '/suksesDebt';
                    const orderId = result.order_id;
                    console.log('Order ID:', orderId);
                    window.location.href =
                        `${finishRedirectUrl}/${orderId}/${sumber_lead}/${metode_pembayaran}`;
                },
                onPending: function(result) {
                    Notiflix.Notify.warning('Pembayaran Pending!');
                    const finishRedirectUrl = '/gagalDebt';
                    const orderId = result.order_id;
                    window.location.href = `${finishRedirectUrl}/${orderId}/${sumber_lead}`;
                },
                onError: function(result) {
                    Notiflix.Notify.failure('Terjadi kesalahan pada pembayaran!');
                    const finishRedirectUrl = '/gagalDebt';
                    const orderId = result.order_id;
                    window.location.href = `${finishRedirectUrl}/${orderId}/${sumber_lead}`;
                },
                onClose: function() {
                    Notiflix.Notify.failure('Pembayaran Ditunda!');
                    const finishRedirectUrl = '/gagalDebt';
                    const orderId = order_id;
                    window.location.href = `${finishRedirectUrl}/${orderId}/${sumber_lead}`;
                }
            });
        }
    </script>
@endsection
