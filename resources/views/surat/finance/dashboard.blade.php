@extends('layouts.admin.app')

@section('title', 'Dashboard Surat Finance')
@section('content')

    {{-- Notifikasi surat masuk ke PCH --}}
    @if(session('suratMarketing') || session('suratAdmin') || session('suratWarehouse') || session('suratPurchasing'))
        <div class="alert alert-warning">
            <strong>Notifikasi!</strong> Ada surat masuk ke <strong>Finance</strong> untuk segera ditindak lanjuti :
            <ul>
                @if(session('suratMarketing'))
                    <li>Dari <strong>Marketing</strong>: {{ session('suratMarketing') }} surat</li>
                @endif
                @if(session('suratAdmin'))
                    <li>Dari <strong>Admin</strong>: {{ session('suratAdmin') }} surat</li>
                @endif
                @if(session('suratWarehouse'))
                    <li>Dari <strong>Warehouse</strong>: {{ session('suratWarehouse') }} surat</li>
                @endif
                @if(session('suratPurchasing'))
                    <li>Dari <strong>Purchasing</strong>: {{ session('suratPurchasing') }} surat</li>
                @endif

            </ul>
        </div>
    @endif
    <h1>Rekap Surat Finance</h1>
    <div class="row">
        <div class="col-md-6">
            <canvas id="statusChart"></canvas>
        </div>
        <div class="col-md-6">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        var ctxStatus = document.getElementById('statusChart').getContext('2d');
        var statusChart = new Chart(ctxStatus, {
            type: 'pie',
            data: {
                labels: ['Pending', 'ACC', 'Tolak'],
                datasets: [{
                    label: 'Status Pengajuan',
                    data: @json([$pending, $acc, $tolak]),
                    backgroundColor: ['#ffc107', '#28a745', '#dc3545']
                }]
            }
        });

        var ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
        var monthlyChart = new Chart(ctxMonthly, {
            type: 'bar',
            data: {
                labels: @json($months),
                datasets: [{
                    label: 'Jumlah Surat per Bulan',
                    data: @json($monthlyCounts),
                    backgroundColor: '#007bff'
                }]
            }
        });

        // Fungsi untuk memutar suara notifikasi
        function playNotificationSound() {
            // Menggunakan asset() untuk menghasilkan URL yang benar
            var audio = new Audio('{{ asset('sounds/notv.wav') }}'); 
            audio.play();
        }

        // Cek apakah ada notifikasi
        @if(session('suratKeFinance') > 0 || session('suratMarketing') > 0 || session('suratAdmin') > 0 || session('suratWarehouse') > 0 || session('suratPurchasing') > 0)
            playNotificationSound(); // Memainkan suara jika ada notifikasi
        @endif

    </script>
    
@endsection
