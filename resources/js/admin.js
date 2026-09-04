let allRequests = [];
let detailModal = null;

// 1. APLICACIÓN INMEDIATA DEL TEMA (Evita el parpadeo blanco al cargar)
(function applyThemeImmediately() {
    const savedTheme = localStorage.getItem("theme") || "light";
    if (savedTheme === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
    } else {
        document.documentElement.removeAttribute("data-theme");
    }
})();

document.addEventListener('DOMContentLoaded', () => {
    // Control del colapso estilo YouTube
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');

    if (btnToggleSidebar) {
        btnToggleSidebar.addEventListener('click', () => {
            document.body.classList.toggle('sidebar-is-collapsed');
            
            // Persistir preferencia
            const isCollapsed = document.body.classList.contains('sidebar-is-collapsed');
            localStorage.setItem('sidebar_collapsed', isCollapsed ? "true" : "false");
        });
    }
});
document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Sincronizar el estado del Sidebar al cargar el DOM ---
    const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
    if (isCollapsed) {
        document.documentElement.classList.add('sidebar-is-collapsed');
    }

    // --- 2. Evento Toggle Sidebar (Guardar preferencia) ---
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');

    if (btnToggleSidebar) {
        btnToggleSidebar.addEventListener('click', () => {
            // Alternar clase en el HTML root
            document.documentElement.classList.toggle('sidebar-is-collapsed');
            
            // Determinar estado actual
            const currentlyCollapsed = document.documentElement.classList.contains('sidebar-is-collapsed');
            
            // Guardar en localStorage para siguientes vistas
            localStorage.setItem('sidebar_collapsed', currentlyCollapsed ? "true" : "false");
        });
    }

    // --- 3. Cerrar Sesión (Reset de Sidebar + Conservar Tema) ---
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', async () => {
            try {
                // Eliminar estado colapsado para que la siguiente sesión empiece abierta por defecto
                localStorage.removeItem('sidebar_collapsed');

                // Petición de Logout a API / Backend
                await fetch('/api/index.php?action=logout');
            } catch (error) {
                console.error('Error al cerrar sesión:', error);
            } finally {
                // Redirigir al Login
                window.location.href = '/';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    // Sincronizar el texto e icono del botón al cargar la página
    updateThemeUI();

    // Configuración Modal
    const modalElement = document.getElementById('detailModal');
    if (modalElement && typeof bootstrap !== 'undefined') {
        detailModal = new bootstrap.Modal(modalElement);
    }

    // Botón Filtro
    const btnFilter = document.getElementById('btnFilter');
    if (btnFilter) btnFilter.addEventListener('click', applyFilters);

    // Botón Cerrar Sesión -> Cierra sesión en PHP y mantiene el tema en localStorage
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', async () => {
            try {
                await fetch('/api/index.php?action=logout');
            } catch (error) {
                console.error('Error al cerrar sesión:', error);
            } finally {
                // Mantiene el localStorage para que al volver al Login conserve el tema
                window.location.href = '/';
            }
        });
    }

    loadRequests();
});

// 2. ESCUCHADOR DE MODO OSCURO (Delegación de Eventos)
document.addEventListener("click", (e) => {
    const themeBtn = e.target.closest("#btnThemeToggle") || e.target.closest("#darkModeToggle");
    if (!themeBtn) return;

    e.preventDefault();

    const isDark = document.documentElement.getAttribute("data-theme") === "dark";
    
    if (isDark) {
        document.documentElement.removeAttribute("data-theme");
        localStorage.setItem("theme", "light");
    } else {
        document.documentElement.setAttribute("data-theme", "dark");
        localStorage.setItem("theme", "dark");
    }

    updateThemeUI();
});

function updateThemeUI() {
    const isDark = document.documentElement.getAttribute("data-theme") === "dark";
    const icon = document.getElementById("themeIcon");
    const text = document.getElementById("themeText");
    const btn = document.getElementById("btnThemeToggle") || document.getElementById("darkModeToggle");

    if (btn) {
        const btnIcon = btn.querySelector("i") || icon;
        const btnText = btn.querySelector("span") || text;

        if (isDark) {
            if (btnIcon) btnIcon.className = "fa-solid fa-sun";
            if (btnText) btnText.innerText = "Claro";
        } else {
            if (btnIcon) btnIcon.className = "fa-solid fa-moon";
            if (btnText) btnText.innerText = "Oscuro";
        }
    }
}

// 3. CARGA DE SOLICITUDES
async function loadRequests() {
    try {
        const res = await fetch('/api/index.php?action=get_admin_requests');
        if (res.status === 401 || res.status === 403) {
            window.location.href = '/';
            return;
        }
        const data = await res.json();
        allRequests = Array.isArray(data) ? data : [];
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