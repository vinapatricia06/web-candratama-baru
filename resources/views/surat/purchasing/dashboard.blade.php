@extends('layouts.admin.app')

@section('title', 'Dashboard Surat Purchasing')

@section('content')

    {{-- Notifikasi surat masuk ke PCH --}}
    @if($suratDM > 0 || $suratADM > 0 || $suratWRH > 0)
        <div class="alert alert-warning">
            <strong>Notifikasi!</strong> Ada surat masuk ke <strong>Purchasing</strong> untuk segera ditindaklanjuti:
            <ul>
                @if($suratDM > 0)
                    <li>Dari <strong>Marketing</strong>: {{ $suratDM }} surat</li>
                @endif
                @if($suratADM > 0)
                    <li>Dari <strong>Admin</strong>: {{ $suratADM }} surat</li>
                @endif
                @if($suratWRH > 0)
                    <li>Dari <strong>Warehouse</strong>: {{ $suratWRH }} surat</li>
                @endif
            </ul>
        </div>
    @endif

    {{-- Notifikasi status surat yang telah diperbarui --}}
    @if(session('statusUpdatedpch'))
        <div class="alert alert-success d-flex justify-content-between align-items-center">
            <span>{{ session('statusUpdatedpch') }}</span>
            <form action="{{ route('notif.clearpch') }}" method="POST" style="margin-left: 10px;">
                @csrf
                <button type="submit" class="btn-close"></button>
            </form>
        </div>
    @endif

    <h1>Rekap Surat Purchasing</h1>
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
                    backgroundColor: ['#f39c12', '#2ecc71', '#e74c3c']
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
                    backgroundColor: '#3498db'
                }]
            }
        });

        // Fungsi untuk memutar suara notifikasi
        function playNotificationSound() {
            var audio = new Audio('{{ asset('sounds/notv.wav') }}');
            audio.play();
        }

        // Cek apakah ada notifikasi surat masuk ke Purchasing
        @if($suratDM > 0 || $suratADM > 0 || $suratWRH > 0)
            playNotificationSound();
        @endif
    </script>

@endsection
