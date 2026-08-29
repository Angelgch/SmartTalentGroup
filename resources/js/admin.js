let allRequests = [];
let detailModal = null;

document.addEventListener('DOMContentLoaded', () => {
    const modalElement = document.getElementById('detailModal');
    if (modalElement) {
        detailModal = new bootstrap.Modal(modalElement);
    }

    const btnFilter = document.getElementById('btnFilter');
    if (btnFilter) {
        btnFilter.addEventListener('click', loadRequests);
    }

    const btnLogout = document.getElementById('btnLogout');
    if (btnLogout) {
        btnLogout.addEventListener('click', logout);
    }

    loadRequests();
});

async function loadRequests() {
    try {
        const res = await fetch('api/index.php?action=get_admin_requests');
        if (res.status === 401 || res.status === 403) {
            window.location.href = 'index.html';
            return;
        }
        allRequests = await res.json();
        renderTable(allRequests);
    } catch (error) {
        console.error('Error cargando solicitudes:', error);
    }
}

function renderTable(data) {
    const tbody = document.getElementById('adminTableBody');
    if (!tbody) return;

    tbody.innerHTML = data.map(r => `
        <tr>
            <td>${r.dni}</td>
            <td>${r.names} ${r.surnames}</td>
            <td><span class="badge ${r.global_status === 'Finalizado' ? 'bg-success' : 'bg-warning'}">${r.global_status}</span></td>
            <td>${r.created_at}</td>
            <td><button class="btn btn-sm btn-info text-white btn-manage" data-id="${r.id}">Gestionar Servicios</button></td>
        </tr>
    `).join('');

    tbody.querySelectorAll('.btn-manage').forEach(btn => {
        btn.addEventListener('click', (e) => {
            const id = parseInt(e.target.getAttribute('data-id'), 10);
            openDetail(id);
        });
    });
}

function openDetail(id) {
    const req = allRequests.find(r => parseInt(r.id, 10) === id);
    if (!req) return;

    let html = `<p><strong>Observaciones Cliente:</strong> ${req.observations || ''}</p><hr>`;
    req.services.forEach(s => {
        html += `
        <div class="card mb-2">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="mb-0">${s.service_name} <span class="badge ${s.status === 'Listo' ? 'bg-success' : 'bg-secondary'}">${s.status}</span></h6>
                </div>
                <div>
                    ${s.status === 'Pendiente' ? `
                        <form data-service-id="${s.id}" class="d-flex form-upload">
                            <input type="file" class="form-control form-control-sm me-2" accept="application/pdf" required>
                            <button type="submit" class="btn btn-sm btn-primary">Subir y Completar</button>
                        </form>
                    ` : `<a href="api/index.php?action=download_pdf&file=${s.pdf_path}" target="_blank" class="btn btn-sm btn-success">Ver PDF Subido</a>`}
                </div>
            </div>
        </div>`;
    });

    const modalContent = document.getElementById('modalContent');
    modalContent.innerHTML = html;

    modalContent.querySelectorAll('.form-upload').forEach(form => {
        form.addEventListener('submit', (e) => {
            const serviceId = form.getAttribute('data-service-id');
            uploadFile(e, serviceId);
        });
    });

    detailModal.show();
}

async function uploadFile(e, serviceId) {
    e.preventDefault();
    const fileInput = e.target.querySelector('input[type="file"]');
    const formData = new FormData();
    formData.append('pdf', fileInput.files[0]);
    formData.append('service_id', serviceId);

    try {
        const res = await fetch('api/index.php?action=upload_pdf', { method: 'POST', body: formData });
        const data = await res.json();
        if (data.status === 'success') {
            alert('Archivo subido. Estado actualizado.');
            detailModal.hide();
            loadRequests();
        } else {
            alert(data.message);
        }
    } catch (error) {
        console.error('Error subiendo archivo:', error);
    }
}

async function logout() {
    await fetch('api/index.php?action=logout');
    window.location.href = 'index.html';
}