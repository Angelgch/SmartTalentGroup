document.addEventListener('DOMContentLoaded', () => {
    const requestForm = document.getElementById('requestForm');
    if (requestForm) {
        requestForm.addEventListener('submit', handleRequestSubmit);
    }

    const tabHistory = document.getElementById('tabHistory');
    if (tabHistory) {
        tabHistory.addEventListener('click', loadHistory);
    }

    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', logout);
    }
});

async function handleRequestSubmit(e) {
    e.preventDefault();
    const checked = Array.from(document.querySelectorAll('#servicesCheckboxes input:checked')).map(cb => cb.value);
    if (checked.length === 0) return alert('Seleccione al menos un servicio.');

    const payload = {
        dni: document.getElementById('dni').value,
        names: document.getElementById('names').value,
        surnames: document.getElementById('surnames').value,
        email: document.getElementById('email').value,
        phone: document.getElementById('phone').value,
        observations: document.getElementById('observations').value,
        services: checked
    };

    try {
        const res = await fetch('api/index.php?action=submit_request', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        });
        const data = await res.json();
        if (data.status === 'error') {
            alert(data.message);
        } else {
            alert('Solicitud creada exitosamente.');
            document.getElementById('requestForm').reset();
        }
    } catch (error) {
        console.error('Error enviando solicitud:', error);
    }
}

async function loadHistory() {
    try {
        const res = await fetch('api/index.php?action=get_client_requests');
        if (res.status === 401 || res.status === 403) {
            window.location.href = 'index.html';
            return;
        }
        const data = await res.json();
        let html = '<div class="accordion" id="accHistory">';
        data.forEach((req) => {
            let badge = req.global_status === 'Finalizado' ? 'bg-success' : 'bg-warning text-dark';
            let servsHtml = req.services.map(s => `
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span>${s.service_name}</span>
                    <span>
                        <span class="badge ${s.status === 'Listo' ? 'bg-success' : 'bg-secondary'}">${s.status}</span>
                        ${s.status === 'Listo' ? `
                            <a href="api/index.php?action=download_pdf&file=${s.pdf_path}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">Ver PDF</a>
                            <a href="api/index.php?action=download_pdf&file=${s.pdf_path}" download class="btn btn-sm btn-primary ms-1">Descargar</a>
                        ` : ''}
                    </span>
                </div>`).join('');

            html += `
            <div class="accordion-item">
                <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#c${req.id}">
                    Candidato: ${req.dni} - ${req.names} | Estado: <span class="badge ${badge} ms-2">${req.global_status}</span>
                </button></h2>
                <div id="c${req.id}" class="accordion-collapse collapse" data-bs-parent="#accHistory">
                    <div class="accordion-body">${servsHtml}</div>
                </div>
            </div>`;
        });
        html += '</div>';
        document.getElementById('historyContainer').innerHTML = html;
    } catch (error) {
        console.error('Error cargando historial:', error);
    }
}

async function logout() {
    await fetch('api/index.php?action=logout');
    window.location.href = 'index.html';
}