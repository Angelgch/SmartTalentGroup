@extends('layouts.admin')

@section('title', 'Solicitudes - SmarTalent')
@section('page-title', 'Lista de Solicitudes')

@section('content')
<!-- Filtro -->
<div class="filter-section">
    <div class="flex-grow-1">
        <label class="form-label text-muted" style="font-size:0.8rem; margin-bottom:2px;"><i class="fas fa-search me-1"></i> Buscar</label>
        <input type="text" id="f_dash_text" class="form-control" placeholder="DNI, nombre o apellido...">
    </div>
    <div style="width: 200px;">
        <label class="form-label text-muted" style="font-size:0.8rem; margin-bottom:2px;"><i class="fas fa-filter me-1"></i> Estado</label>
        <select id="f_dash_status" class="form-select">
            <option value="">Todos los estados</option>
            <option value="Pendiente">Pendiente</option>
            <option value="En Proceso">En Progreso</option>
            <option value="Finalizado">Realizado</option>
        </select>
    </div>
    <div class="d-flex align-items-end" style="height: 52px; gap: 8px;">
        <button class="btn btn-gestionar h-100 px-4" onclick="filterDashboard()"><i class="fas fa-search me-1"></i> Filtrar</button>
        <button class="btn btn-light border h-100 px-4" onclick="clearFilters()"><i class="fas fa-times me-1"></i> Limpiar</button>
    </div>
</div>

<!-- Tabla Principal -->
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
        <tbody id="solicitudesTableBody">
            <tr><td colspan="7" class="text-center py-4">Cargando datos...</td></tr>
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
let allRequests = [];
let filteredRequests = [];

document.addEventListener('DOMContentLoaded', () => {
    const params = new URLSearchParams(window.location.search);
    const initialStatus = params.get('status');
    if (initialStatus) document.getElementById('f_dash_status').value = initialStatus;

    loadRequests();
});

async function loadRequests() {
    try {
        const res = await fetch('/api/index.php?action=get_admin_requests');
        const data = await res.json();
        allRequests = Array.isArray(data) ? data : [];
        filterDashboard();

        const reqId = new URLSearchParams(window.location.search).get('id');
        if (reqId) openDetail(reqId);
    } catch (e) {
        document.getElementById('solicitudesTableBody').innerHTML = '<tr><td colspan="7" class="text-center py-4 text-danger">Error al cargar solicitudes.</td></tr>';
    }
}

function renderTable() {
    const tbody = document.getElementById('solicitudesTableBody');
    if (filteredRequests.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted"><i class="fas fa-inbox fs-4 d-block mb-2"></i>No hay registros.</td></tr>';
        return;
    }

    tbody.innerHTML = filteredRequests.map(r => {
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
                <div class="d-flex justify-content-center gap-2">
                    <button class="btn-gestionar" onclick="openDetail(${r.id})"><i class="fas fa-cog me-1"></i>Gestionar</button>
                    <button class="btn btn-sm btn-danger-custom p-2" title="Eliminar solicitud" onclick="deleteRequest(${r.id})"><i class="fas fa-trash-alt"></i></button>
                </div>
            </td>
        </tr>`;
    }).join('');
}

function filterDashboard() {
    const text = document.getElementById('f_dash_text').value.toLowerCase();
    const status = document.getElementById('f_dash_status').value;
    filteredRequests = allRequests.filter(r => {
        const matchText = r.dni.toLowerCase().includes(text) || r.names.toLowerCase().includes(text) || r.surnames.toLowerCase().includes(text);
        const matchStatus = status ? r.global_status === status : true;
        return matchText && matchStatus;
    });
    renderTable();
}

function clearFilters() {
    document.getElementById('f_dash_text').value = '';
    document.getElementById('f_dash_status').value = '';
    filteredRequests = [...allRequests];
    renderTable();
}

function openDetail(id) {
    const req = allRequests.find(r => r.id == id);
    if (!req) return;

    let html = `
    <div class="mb-4 p-3 rounded req-header-box" style="border-left: 4px solid var(--teal);">
        <h6 class="mb-1 req-name-text fw-bold">${req.names} ${req.surnames}</h6>
        <small class="text-muted"><i class="fas fa-id-card me-1"></i>DNI: ${req.dni} &nbsp;|&nbsp; <i class="fas fa-phone me-1"></i>${req.phone}</small>
        ${req.observations ? `<div class="mt-2 req-obs-text"><i class="fas fa-comment-dots text-warning me-1"></i> <em>${req.observations}</em></div>` : ''}
    </div>`;

    if (req.services && req.services.length > 0) {
        req.services.forEach(s => {
            const isDone = s.status === 'Listo';
            html += `
            <div class="service-card ${isDone ? 'done' : ''} mb-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" style="width: 2.5em; height: 1.25em; cursor:pointer;"
                                ${isDone ? 'checked' : ''} onchange="toggleServiceStatus(${s.id}, this.checked, ${req.id})">
                        </div>
                        <div>
                            <span class="fw-bold service-name-text d-block" style="font-size:0.95rem">${s.service_name}</span>
                            <span class="badge ${isDone ? 'bg-success' : 'bg-warning text-dark'}">${s.status}</span>
                        </div>
                    </div>
                    <div>
                        ${isDone && s.pdf_path ? `
                            <a href="/api/index.php?action=download_pdf&file=${s.pdf_path}&view=1" target="_blank" class="btn btn-sm text-success fw-bold" style="background: rgba(52,199,89,0.15);">
                                <i class="fas fa-file-pdf me-1"></i>Ver PDF
                            </a>
                        ` : ''}
                    </div>
                </div>
                ${!isDone ? `
                <div class="mt-3 p-3 border rounded upload-box" style="border-style: dashed !important; border-color: var(--border) !important;">
                    <form onsubmit="uploadFile(event, ${s.id})" class="d-flex align-items-center gap-3">
                        <input type="file" class="form-control form-control-sm" accept="application/pdf" required>
                        <button type="submit" class="btn btn-primary btn-sm text-nowrap fw-bold px-3">
                            <i class="fas fa-cloud-upload-alt me-1"></i> Subir
                        </button>
                    </form>
                </div>` : ''}
            </div>`;
        });
    } else {
        html += `<div class="text-center text-muted py-3">No hay servicios solicitados.</div>`;
    }

    document.getElementById('modalContent').innerHTML = html;
    new bootstrap.Modal(document.getElementById('detailModal')).show();
}

async function toggleServiceStatus(serviceId, isChecked, requestId) {
    const newStatus = isChecked ? 'Listo' : 'Pendiente';
    try {
        const res = await fetch('/api/index.php?action=toggle_service_status', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ service_id: serviceId, status: newStatus })
        });
        if ((await res.json()).status === 'success') {
            await loadRequests();
            openDetail(requestId);
        }
    } catch (e) {}
}

async function uploadFile(e, serviceId) {
    e.preventDefault();
    const formData = new FormData();
    formData.append('pdf', e.target.querySelector('input[type="file"]').files[0]);
    formData.append('service_id', serviceId);

    try {
        const res = await fetch('/api/index.php?action=upload_pdf', { method: 'POST', body: formData });
        if ((await res.json()).status === 'success') {
            bootstrap.Modal.getInstance(document.getElementById('detailModal')).hide();
            await loadRequests();
        }
    } catch (e) {}
}

async function deleteRequest(id) {
    if (!confirm("¿Estás seguro de eliminar esta solicitud permanentemente?")) return;
    try {
        const res = await fetch('/api/index.php?action=delete_request', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        if ((await res.json()).status === 'success') loadRequests();
    } catch (e) {}
}
</script>
@endpush