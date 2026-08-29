let allRequests = [];
let detailModal = null;

document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('detailModal');
    if (modalElement && typeof bootstrap !== 'undefined') {
        detailModal = new bootstrap.Modal(modalElement);
    }

    const btnFilter = document.getElementById('btnFilter');
    if (btnFilter) btnFilter.addEventListener('click', applyFilters);

    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) btnLogout.addEventListener('click', logout);

    const darkModeToggle = document.getElementById('darkModeToggle');
    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', () => {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            if (currentTheme === 'dark') {
                document.documentElement.removeAttribute('data-theme');
            } else {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        });
    }

    loadRequests();
});

function switchSection(section) {
    document.querySelectorAll('.section-view').forEach(el => el.classList.remove('active'));
    
    const targetSection = document.getElementById(`view-${section}`);
    if (targetSection) targetSection.classList.add('active');

    document.querySelectorAll('.sidebar-nav a').forEach(el => el.classList.remove('active'));
    const activeNav = document.getElementById(`nav-${section}`);
    if (activeNav) activeNav.classList.add('active');

    const titles = {
        'dashboard': 'Panel de Administración',
        'solicitudes': 'Lista de Solicitudes',
        'lote': 'Reporte Detallado de Lote',
        'reportes': 'Reportes y Estadísticas'
    };
    
    const pageTitle = document.getElementById('pageTitle');
    if (pageTitle) {
        pageTitle.innerHTML = `<i class="fas fa-shield-halved me-2" style="color:var(--teal)"></i>${titles[section] || titles['dashboard']}`;
    }
}

async function loadRequests() {
    try {
        const res = await fetch('/api/get_admin_requests');
        if (res.status === 401 || res.status === 403) {
            window.location.href = '/login';
            return;
        }
        allRequests = await res.json();
        updateMetrics(allRequests);
        renderTable(allRequests);
    } catch (error) {
        console.error('Error cargando solicitudes:', error);
        renderTable([]);
    }
}

function updateMetrics(data) {
    const total = data.length;
    const pendientes = data.filter(r => r.global_status === 'Pendiente').length;
    const proceso = data.filter(r => r.global_status === 'En Proceso').length;
    const finalizados = data.filter(r => r.global_status === 'Finalizado').length;

    if (document.getElementById('dashTotal')) document.getElementById('dashTotal').innerText = total;
    if (document.getElementById('dashPendientes')) document.getElementById('dashPendientes').innerText = pendientes;
    if (document.getElementById('dashProceso')) document.getElementById('dashProceso').innerText = proceso;
    if (document.getElementById('dashFinalizados')) document.getElementById('dashFinalizados').innerText = finalizados;
}

function renderTable(data) {
    const tbody = document.getElementById('adminTableBody');
    if (!tbody) return;

    if (!data || data.length === 0) {
        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted"><i class="fas fa-inbox fs-4 d-block mb-2"></i>No hay solicitudes registradas.</td></tr>`;
        return;
    }

    tbody.innerHTML = data.map(r => `
        <tr>
            <td class="fw-bold">${r.dni}</td>
            <td>${r.names} ${r.surnames}</td>
            <td>
                <span class="badge ${r.global_status === 'Finalizado' ? 'bg-success' : (r.global_status === 'En Proceso' ? 'bg-info' : 'bg-warning')}">
                    ${r.global_status}
                </span>
            </td>
            <td>${r.created_at}</td>
            <td class="text-center">
                <button class="btn-gestionar btn-manage" data-id="${r.id}">
                    <i class="fas fa-cog me-1"></i>Gestionar Servicios
                </button>
            </td>
        </tr>
    `).join('');
}

function applyFilters() {
    const textVal = (document.getElementById('f_text')?.value || '').toLowerCase();
    const statusVal = document.getElementById('f_status')?.value || '';

    const filtered = allRequests.filter(r => {
        const matchesText = r.dni.toLowerCase().includes(textVal) || 
                            `${r.names} ${r.surnames}`.toLowerCase().includes(textVal);
        const matchesStatus = statusVal === '' || r.global_status === statusVal;
        return matchesText && matchesStatus;
    });

    renderTable(filtered);
}

async function logout() {
    window.location.href = '/login';
}