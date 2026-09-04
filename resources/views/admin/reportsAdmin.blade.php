@extends('layouts.admin')

@section('title', 'Reportes - SmarTalent')
@section('page-title', 'Reportes y Estadísticas')

@section('content')
<div class="row g-4">
    <div class="col-md-6">
        <div class="section-card p-4 rounded-4 h-100">
            <h6 class="mb-4 text-center fw-bold text-muted">Estado Global de Solicitudes</h6>
            <div style="height: 300px; display: flex; justify-content: center;">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="section-card p-4 rounded-4 h-100">
            <h6 class="mb-4 text-center fw-bold text-muted">Servicios más solicitados</h6>
            <div style="height: 300px;">
                <canvas id="servicesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mt-1">
    <div class="col-md-6">
        <div class="section-card p-4 rounded-4 h-100">
            <h6 class="mb-4 text-center fw-bold text-muted">Evolución de Solicitudes (Últimos 7 días)</h6>
            <div style="height: 300px;">
                <canvas id="datesChart"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="section-card p-4 rounded-4 h-100">
            <h6 class="mb-4 text-center fw-bold text-muted">Top Clientes Activos</h6>
            <div style="height: 300px;">
                <canvas id="topClientsChart"></canvas>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', generateCharts);

async function generateCharts() {
    try {
        const res = await fetch('/api/index.php?action=get_admin_requests');
        const allRequests = await res.json();
        if (!Array.isArray(allRequests)) return;

        // Chart 1: Status
        const counts = { 'Pendiente': 0, 'En Proceso': 0, 'Finalizado': 0 };
        allRequests.forEach(r => { if (counts[r.global_status] !== undefined) counts[r.global_status]++; });

        new Chart(document.getElementById('statusChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Pendiente', 'En Progreso', 'Realizado'],
                datasets: [{
                    data: [counts['Pendiente'], counts['En Proceso'], counts['Finalizado']],
                    backgroundColor: ['#94a3b8', '#f5a623', '#27b6a9'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });

        // Chart 2: Services
        const svcCounts = {};
        allRequests.forEach(r => {
            if (r.services) {
                r.services.forEach(s => { svcCounts[s.service_name] = (svcCounts[s.service_name] || 0) + 1; });
            }
        });

        new Chart(document.getElementById('servicesChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: Object.keys(svcCounts).map(l => l.length > 15 ? l.substring(0, 15) + '...' : l),
                datasets: [{ label: 'Veces Solicitado', data: Object.values(svcCounts), backgroundColor: '#eb4f6c', borderRadius: 6 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // Chart 3: Evolution
        const dateCounts = {};
        const today = new Date();
        for (let i = 6; i >= 0; i--) {
            const d = new Date(today);
            d.setDate(d.getDate() - i);
            dateCounts[d.toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit' })] = 0;
        }
        allRequests.forEach(r => {
            const dateStr = new Date(r.created_at).toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit' });
            if (dateCounts[dateStr] !== undefined) dateCounts[dateStr]++;
        });

        new Chart(document.getElementById('datesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: Object.keys(dateCounts),
                datasets: [{ label: 'Solicitudes', data: Object.values(dateCounts), borderColor: '#30c1ac', backgroundColor: 'rgba(48,193,172,0.1)', fill: true, tension: 0.3 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // Chart 4: Top Clients
        const clientCounts = {};
        allRequests.forEach(r => {
            const cName = r.client_email ? r.client_email.split('@')[0] : 'Admin';
            clientCounts[cName] = (clientCounts[cName] || 0) + 1;
        });
        const sortedClients = Object.entries(clientCounts).sort((a, b) => b[1] - a[1]).slice(0, 5);

        new Chart(document.getElementById('topClientsChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: sortedClients.map(c => c[0].length > 10 ? c[0].substring(0, 10) + '...' : c[0]),
                datasets: [{ label: 'Solicitudes', data: sortedClients.map(c => c[1]), backgroundColor: '#30c1ac', borderRadius: 6 }]
            },
            options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
        });
    } catch (e) {}
}
</script>
@endpush