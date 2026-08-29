document.addEventListener('DOMContentLoaded', () => {
    // Evento Formulario Nueva Solicitud
    const requestForm = document.getElementById('requestForm');
    if (requestForm) {
        requestForm.addEventListener('submit', handleRequestSubmit);
    }

    // Evento Pestaña Seguimiento / Historial
    const tabHistory = document.getElementById('tabHistory');
    if (tabHistory) {
        tabHistory.addEventListener('click', loadHistory);
    }

    // Evento Cerrar Sesión
    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', logout);
    }
});

/* 1. ENVIAR FORMULARIO DE NUEVA SOLICITUD */
async function handleRequestSubmit(e) {
    e.preventDefault();

    // Obtener servicios seleccionados
    const checked = Array.from(document.querySelectorAll('#servicesCheckboxes input:checked')).map(cb => cb.value);
    if (checked.length === 0) {
        alert('Seleccione al menos un servicio.');
        return;
    }

    // Obtener Token CSRF de Laravel
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    const payload = {
        dni: document.getElementById('dni').value.trim(),
        names: document.getElementById('names').value.trim(),
        surnames: document.getElementById('surnames').value.trim(),
        email: document.getElementById('email').value.trim(),
        phone: document.getElementById('phone').value.trim(),
        observations: document.getElementById('observations').value.trim(),
        services: checked
    };

    try {
        const res = await fetch('/api/requests', {
            method: 'POST',
            headers: { 
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (!res.ok || data.status === 'error') {
            alert(data.message || 'Error al procesar la solicitud.');
        } else {
            alert('Solicitud creada exitosamente.');
            document.getElementById('requestForm').reset();
        }
    } catch (error) {
        console.error('Error enviando solicitud:', error);
        alert('Ocurrió un problema de conexión al guardar la solicitud.');
    }
}

/* 2. CARGAR HISTORIAL DE SEGUIMIENTO EN EL ACORDEÓN */
async function loadHistory() {
    const historyContainer = document.getElementById('historyContainer');
    if (!historyContainer) return;

    historyContainer.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2 text-muted">Cargando solicitudes...</p>
        </div>`;

    try {
        const res = await fetch('/api/requests');

        if (res.status === 401 || res.status === 403) {
            window.location.href = '/';
            return;
        }

        const data = await res.json();

        if (!Array.isArray(data) || data.length === 0) {
            historyContainer.innerHTML = '<div class="alert alert-info text-center">No registra solicitudes actualmente.</div>';
            return;
        }

        let html = '<div class="accordion" id="accHistory">';
        data.forEach((req) => {
            let badge = req.global_status === 'Finalizado' ? 'bg-success' : 'bg-warning text-dark';

            let servsHtml = req.services.map(s => `
                <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                    <span>${s.service_name}</span>
                    <span>
                        <span class="badge ${s.status === 'Listo' ? 'bg-success' : 'bg-secondary'}">${s.status}</span>
                        ${s.status === 'Listo' ? `
                            <a href="/downloads/pdf/${s.pdf_path}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Ver PDF</a>
                            <a href="/downloads/pdf/${s.pdf_path}" download class="btn btn-sm btn-primary ms-1">Descargar</a>
                        ` : ''}
                    </span>
                </div>`).join('');

            html += `
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c${req.id}">
                        Candidato: ${req.dni} - ${req.names} ${req.surnames} | Estado: <span class="badge ${badge} ms-2">${req.global_status}</span>
                    </button>
                </h2>
                <div id="c${req.id}" class="accordion-collapse collapse" data-bs-parent="#accHistory">
                    <div class="accordion-body">
                        ${servsHtml}
                    </div>
                </div>
            </div>`;
        });
        html += '</div>';

        historyContainer.innerHTML = html;
    } catch (error) {
        console.error('Error cargando historial:', error);
        historyContainer.innerHTML = '<div class="alert alert-danger text-center">No se pudo obtener el historial.</div>';
    }
}

/* 3. LOGOUT / CERRAR SESIÓN */
async function logout() {
    window.location.href = '/';
}