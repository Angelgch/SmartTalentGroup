let allRequests = [];
let detailModal = null;
let currentZoom = 100;

// 1. APLICACIÓN INMEDIATA DEL TEMA (Evita parpadeo)
(function applyThemeImmediately() {
    const savedTheme = localStorage.getItem("theme") || "light";
    if (savedTheme === "dark") {
        document.documentElement.setAttribute("data-theme", "dark");
    } else {
        document.documentElement.removeAttribute("data-theme");
    }
})();

document.addEventListener('DOMContentLoaded', () => {
    
    // --- 1. Sincronizar estado del Sidebar ---
    const isCollapsed = localStorage.getItem('sidebar_collapsed') === 'true';
    if (isCollapsed) {
        document.documentElement.classList.add('sidebar-is-collapsed');
    }

    // --- 2. Evento Toggle Sidebar ---
    const btnToggleSidebar = document.getElementById('btnToggleSidebar');
    if (btnToggleSidebar) {
        btnToggleSidebar.addEventListener('click', () => {
            document.documentElement.classList.toggle('sidebar-is-collapsed');
            const currentlyCollapsed = document.documentElement.classList.contains('sidebar-is-collapsed');
            localStorage.setItem('sidebar_collapsed', currentlyCollapsed ? "true" : "false");
        });
    }

    // --- 3. Actualizar UI de Tema ---
    updateThemeUI();

    const modalElement = document.getElementById('detailModal');
    if (modalElement && typeof bootstrap !== 'undefined') {
        detailModal = new bootstrap.Modal(modalElement);
    }

    const btnFilter = document.getElementById('btnFilter');
    if (btnFilter) btnFilter.addEventListener('click', applyFilters);

    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', async () => {
            try {
                localStorage.removeItem('sidebar_collapsed');
                await fetch('/api/index.php?action=logout');
            } catch (error) {
                console.error('Error al cerrar sesión:', error);
            } finally {
                window.location.href = '/';
            }
        });
    }

    loadRequests();
});

// 2. MODO OSCURO (Delegación de Eventos)
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

// 3. FUNCIONES GLOBALES DE ACCESIBILIDAD
window.adjustFontSize = function(delta) {
    currentZoom += delta * 10;
    if (currentZoom >= 80 && currentZoom <= 130) {
        document.body.style.zoom = currentZoom + "%";
    }
};

window.toggleDyslexicFont = function() {
    document.body.classList.toggle('font-dyslexic');
};

window.toggleTextSpacing = function() {
    document.body.classList.toggle('wide-spacing');
};

window.toggleHighContrast = function() {
    document.documentElement.classList.toggle('high-contrast');
};

window.setDaltonism = function(type) {
    document.documentElement.classList.remove('filter-grayscale', 'filter-deuteranopia', 'filter-protanopia');
    if (type !== 'none') {
        document.documentElement.classList.add('filter-' + type);
    }
};

window.resetAccessibility = function() {
    currentZoom = 100;
    document.body.style.zoom = "100%";
    document.body.classList.remove('font-dyslexic', 'wide-spacing');
    document.documentElement.classList.remove(
        'high-contrast', 
        'filter-grayscale', 
        'filter-deuteranopia', 
        'filter-protanopia'
    );
};

// 4. CARGA Y GESTIÓN DE SOLICITUDES
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