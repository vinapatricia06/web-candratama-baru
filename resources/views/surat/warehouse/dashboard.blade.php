@extends('layouts.admin.app')

@section('title', 'Dashboard Surat Warehouse')
@section('content')

    @if($suratKeWarehouse > 0)
        <div class="alert alert-warning">
            Ada {{ $suratKeWarehouse }} surat yang masuk ke Warehouse dengan status Pending.
        </div>
    @endif

    @if(session('statusUpdatedwrh'))
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <span>{{ session('statusUpdatedwrh') }}</span>
            <form action="{{ route('notif.clearwrh') }}" method="POST" style="margin-left: 10px;">
                @csrf
                <button type="submit" class="btn-close"></button>
            </form>
        </div>
    @endif

    <h1>Rekap Surat Warehouse</h1>
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
            var audio = new Audio('{{ asset('sounds/notv.wav') }}'); 
            audio.play();
        }

        // Cek apakah ada notifikasi
        @if(session('suratKeWarehouse') > 0)
            playNotificationSound();
        @endif

    </script>
@endsection
