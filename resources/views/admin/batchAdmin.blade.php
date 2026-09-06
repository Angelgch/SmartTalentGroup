@extends('layouts.admin')

@section('title', 'Lote Detallado - SmarTalent')
@section('page-title', 'Reporte Detallado de Lote')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="m-0" style="color: #2c3e50; font-weight: 700;">
        Solicitud del Lote - Fecha: <span id="currentDateStr"></span>
    </h5>
    <button class="btn-excel" onclick="downloadExcel()">
        <i class="fas fa-file-excel me-1"></i> Descargar Excel
    </button>
</div>

<div class="table-responsive-custom">
    <table class="data-table-new" id="loteTable">
        <thead>
            <tr>
                <th>N°</th>
                <th>FECHA SOLICITUD</th>
                <th>RESPONSABLE</th>
                <th>DNI</th>
                <th>NOMBRES Y APELLIDOS</th>
                <th>ESTADO GENERAL</th>
                <th>ANT. NACIONALES</th>
                <th>VERIF. CREDITICIAS</th>
                <th>VERIF. LABORALES</th>
                <th>RECORD LABORAL</th>
                <th>VERIF. ACADÉMICAS</th>
                <th>RES. DOMICILIARIOS</th>
                <th>ARCHIVOS</th>
                <th>BORRAR</th>
            </tr>
        </thead>
        <tbody id="loteTableBody">
            <tr><td colspan="14" class="text-center py-4">Cargando datos...</td></tr>
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
let allRequests = [];
const SERVICE_COLUMNS = [
    'Antecedentes Nacionales',
    'Verificaciones Crediticias',
    'Verificaciones Laborales',
    'Récord Laboral',
    'Verificaciones Académicas',
    'Verificaciones Domiciliarias'
];

document.addEventListener('DOMContentLoaded', () => {
    document.getElementById('currentDateStr').textContent = new Date().toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
    loadLoteData();
});

async function loadLoteData() {
    try {
        const res = await fetch('/api/index.php?action=get_admin_requests');
        const data = await res.json();
        allRequests = Array.isArray(data) ? data : [];
        renderLoteTable();
    } catch (e) {
        document.getElementById('loteTableBody').innerHTML = '<tr><td colspan="14" class="text-center py-4 text-danger">Error al cargar reporte.</td></tr>';
    }
}

function renderLoteTable() {
    const tbody = document.getElementById('loteTableBody');
    if (allRequests.length === 0) {
        tbody.innerHTML = '<tr><td colspan="14" class="text-center py-4 text-muted">No hay solicitudes registradas.</td></tr>';
        return;
    }

    tbody.innerHTML = allRequests.map((r, index) => {
        let badgeClass = 'badge-pendiente';
        let statusText = 'Pendiente';
        if (r.global_status === 'Finalizado') { badgeClass = 'badge-realizado'; statusText = 'Realizado'; }
        else if (r.global_status === 'En Proceso') { badgeClass = 'badge-progreso'; statusText = 'En Progreso'; }

        const date = new Date(r.created_at).toLocaleDateString('es-PE', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const clientEmail = r.client_email ? r.client_email.split('@')[0] : 'Admin';

        let checkboxesHtml = '';
        SERVICE_COLUMNS.forEach(colName => {
            const svc = r.services ? r.services.find(s => s.service_name.includes(colName) || colName.includes(s.service_name)) : null;
            if (svc) {
                checkboxesHtml += `<td><div class="check-box-ui checked"><i class="fas fa-check" style="font-size: 0.6rem;"></i></div></td>`;
            } else {
                checkboxesHtml += `<td><div class="check-box-ui"></div></td>`;
            }
        });

        const filesCount = r.services ? r.services.filter(s => s.pdf_path).length : 0;

        return `
        <tr>
            <td>${index + 1}</td>
            <td>${date}</td>
            <td>${clientEmail}</td>
            <td>${r.dni}</td>
            <td class="text-start fw-bold" style="color: #2c3e50;">${r.names} ${r.surnames}</td>
            <td><span class="badge-status ${badgeClass}">${statusText}</span></td>
            ${checkboxesHtml}
            <td><a href="{{ route('admin.requests') }}?id=${r.id}" class="btn-archivos">Archivos (${filesCount})</a></td>
            <td>
                <button class="btn btn-sm text-danger border-0 p-0" title="Eliminar registro" onclick="deleteRequest(${r.id})">
                    <i class="fas fa-trash-alt fs-6"></i>
                </button>
            </td>
        </tr>`;
    }).join('');
}

function downloadExcel() {
    if (allRequests.length === 0) return alert("No hay datos para exportar");

    const header = ["N°", "Fecha Solicitud", "Responsable", "DNI", "Nombres", "Apellidos", "Correo", "Teléfono", "Observaciones", "Estado General"];
    SERVICE_COLUMNS.forEach(col => header.push(col));

    const data = [header];
    allRequests.forEach((r, index) => {
        const date = new Date(r.created_at).toLocaleDateString('es-PE');
        let row = [
            index + 1, date, r.client_email || 'Admin', r.dni, r.names, r.surnames,
            r.email, r.phone, r.observations || '', r.global_status
        ];

        SERVICE_COLUMNS.forEach(colName => {
            const svc = r.services ? r.services.find(s => s.service_name.includes(colName) || colName.includes(s.service_name)) : null;
            row.push(svc ? svc.status : 'No Solicitado');
        });
        data.push(row);
    });

    const ws = XLSX.utils.aoa_to_sheet(data);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, "Solicitudes");
    XLSX.writeFile(wb, 'Reporte_Solicitudes.xlsx');
}

async function deleteRequest(id) {
    if (!confirm("¿Estás seguro de eliminar este registro?")) return;
    try {
        const res = await fetch('/api/index.php?action=delete_request', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        });
        if ((await res.json()).status === 'success') loadLoteData();
    } catch (e) {}
}
</script>
@endpush