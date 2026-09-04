@extends('layouts.admin')

@section('title', 'Dashboard - SmarTalent')
@section('page-title', 'Panel de Administración')

@section('content')
<!-- Tarjetas de Estadísticas -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='{{ route('admin.requests') }}'">
            <div class="stat-icon teal"><i class="fas fa-clipboard-list"></i></div>
            <div>
                <div class="stat-number" id="dashTotal">0</div>
                <div class="stat-label">Total Solicitudes</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='{{ route('admin.requests') }}?status=Pendiente'">
            <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
            <div>
                <div class="stat-number" id="dashPendientes">0</div>
                <div class="stat-label">Pendientes</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='{{ route('admin.requests') }}?status=En Proceso'">
            <div class="stat-icon pink"><i class="fas fa-spinner"></i></div>
            <div>
                <div class="stat-number" id="dashProceso">0</div>
                <div class="stat-label">En Proceso</div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-6">
        <div class="stat-card" style="cursor: pointer;" onclick="window.location.href='{{ route('admin.requests') }}?status=Finalizado'">
            <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-number" id="dashFinalizados">0</div>
                <div class="stat-label">Finalizados</div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h6 class="m-0 text-muted fw-bold">Solicitudes Recientes</h6>
    <a href="{{ route('admin.requests') }}" class="btn btn-sm btn-outline-secondary">Ver todas</a>
</div>

<!-- Tabla Resumen -->
<div class="table-custom-container">
    <table class="table-simple">
        <thead>
            <tr>
                <th>DNI</th>
                <th>NOMBRES Y APELLIDOS</th>
                <th>CORREO CLIENTE</th>
                <th>ESTADO</th>
                <th>FECHA</th>
                <th>PROGRESO</th>
                <th class="text-center">ACCIONES</th>
            </tr>
        </thead>
        <tbody id="dashTableBody">
            <tr><td colspan="7" class="text-center py-4">Cargando datos...</td></tr>
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
let allRequests = [];

document.addEventListener('DOMContentLoaded', loadDashboardData);

async function loadDashboardData() {
    try {
        const res = await fetch('/api/index.php?action=get_admin_requests');
        if (res.status === 401 || res.status === 403) { window.location.href = '/login'; return; }
        const data = await res.json();
        allRequests = Array.isArray(data) ? data : [];
        renderDashboard();
    } catch (e) {
        document.getElementById('dashTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Error al cargar datos.</td></tr>';
    }
}

function renderDashboard() {
    document.getElementById('dashTotal').textContent = allRequests.length;
    document.getElementById('dashPendientes').textContent = allRequests.filter(r => r.global_status === 'Pendiente').length;
    document.getElementById('dashProceso').textContent = allRequests.filter(r => r.global_status === 'En Proceso').length;
    document.getElementById('dashFinalizados').textContent = allRequests.filter(r => r.global_status === 'Finalizado').length;

    const dashTbody = document.getElementById('dashTableBody');
    if (allRequests.length === 0) {
        dashTbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-inbox fs-4 d-block mb-2"></i>No hay registros.</td></tr>';
        return;
    }

    dashTbody.innerHTML = allRequests.slice(0, 5).map(r => {
        let badgeClass = 'badge-pendiente';
        let statusText = 'Pendiente';
        if (r.global_status === 'Finalizado') { badgeClass = 'badge-realizado'; statusText = 'Realizado'; }
        else if (r.global_status === 'En Proceso') { badgeClass = 'badge-progreso'; statusText = 'En Progreso'; }

        const date = new Date(r.created_at).toLocaleDateString('es-PE', { day: 'numeric', month: 'short', year: 'numeric' });
        const totalServ = r.services ? r.services.length : 0;
        const doneServ = r.services ? r.services.filter(s => s.status === 'Listo').length : 0;
        const pct = totalServ > 0 ? (doneServ / totalServ) * 100 : 0;

        return `
        <tr>
            <td class="fw-bold">${r.dni}</td>
            <td>${r.names} ${r.surnames}</td>
            <td class="text-muted" style="font-size:0.75rem">${r.client_email || 'Desconocido'}</td>
            <td><span class="badge-status ${badgeClass}">${statusText}</span></td>
            <td>${date}</td>
            <td>
                <div class="progress-indicator">
                    <div class="progress-bar-bg"><div class="progress-bar-fill" style="width:${pct}%"></div></div>
                    <span class="progress-text">${doneServ}/${totalServ}</span>
                </div>
            </td>
            <td class="text-center">
                <a href="{{ route('admin.requests') }}?id=${r.id}" class="btn-gestionar"><i class="fas fa-cog me-1"></i>Gestionar</a>
            </td>
        </tr>`;
    }).join('');
}
</script>
@endpush