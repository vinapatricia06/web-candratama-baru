import Chart from 'chart.js/auto';

// Ambil data dari controller (misalnya lewat Laravel Blade)
const labels = @json(array_keys($data)); // Tahun (misal: 2024, 2025)
const dataOmset = @json(array_map('array_sum', $data)); // Total omset tiap tahun

const ctx = document.getElementById('chartTahun').getContext('2d');
const myChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [{
            label: 'Total Omset Tahunan',
            data: dataOmset,
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
