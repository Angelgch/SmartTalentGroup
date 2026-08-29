@extends('layouts.app')

@section('title', 'Panel de Administrador - SmarTalent')

@section('content')
<!-- Enlaces a FontAwesome y tus archivos unificados en public/ -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- <link rel="stylesheet" href="{{ asset('css/admin.css') }}"> -->
@vite(['resources/css/admin.css', 'resources/js/admin.js'])

<!-- Sidebar / Menú Lateral -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <img src="https://www.stgconsultoria.com/imagenes/logo1.png" alt="SmarTalent Group">
        <h6>Panel Administrador</h6>
    </div>
    <nav class="sidebar-nav">
        <a onclick="switchSection('dashboard')" id="nav-dashboard" class="active"><i class="fas fa-th-large"></i> Dashboard</a>
        <a onclick="switchSection('solicitudes')" id="nav-solicitudes"><i class="fas fa-list"></i> Solicitudes</a>
        <a onclick="switchSection('lote')" id="nav-lote"><i class="fas fa-table"></i> Lote Detallado</a>
        <a onclick="switchSection('reportes')" id="nav-reportes"><i class="fas fa-chart-pie"></i> Reportes</a>
    </nav>
    <div class="sidebar-footer">
        <button id="btnLogout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
    </div>
</aside>

<!-- Contenido Principal -->
<div class="main-content">
    <div class="topbar">
        <div class="d-flex align-items-center gap-3">
            <h5 id="pageTitle"><i class="fas fa-shield-halved me-2" style="color:var(--teal)"></i>Panel de Administración</h5>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button id="darkModeToggle" class="dark-mode-btn"><i class="fas fa-moon me-1"></i> Modo Oscuro</button>
            <span class="badge-info"><i class="fas fa-user-shield me-1"></i> Administrador</span>
        </div>
    </div>

    <div class="p-4 flex-grow-1">
        <!-- VISTA: DASHBOARD -->
        <div id="view-dashboard" class="section-view active">
            <div class="row g-3 mb-4">
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card" style="cursor: pointer;" onclick="switchSection('solicitudes')">
                        <div class="stat-icon teal"><i class="fas fa-clipboard-list"></i></div>
                        <div>
                            <div class="stat-number" id="dashTotal">0</div>
                            <div class="stat-label">Total Solicitudes</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card" style="cursor: pointer;" onclick="switchSection('solicitudes')">
                        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
                        <div>
                            <div class="stat-number" id="dashPendientes">0</div>
                            <div class="stat-label">Pendientes</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card" style="cursor: pointer;" onclick="switchSection('solicitudes')">
                        <div class="stat-icon pink"><i class="fas fa-spinner"></i></div>
                        <div>
                            <div class="stat-number" id="dashProceso">0</div>
                            <div class="stat-label">En Proceso</div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6">
                    <div class="stat-card" style="cursor: pointer;" onclick="switchSection('solicitudes')">
                        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
                        <div>
                            <div class="stat-number" id="dashFinalizados">0</div>
                            <div class="stat-label">Finalizados</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="filter-section">
                <div class="flex-grow-1">
                    <label for="f_text" class="form-label text-muted small fw-bold mb-1">Buscar Candidato</label>
                    <input type="text" id="f_text" class="form-control" placeholder="Ingrese DNI, nombre o apellido...">
                </div>
                <div style="width: 200px;">
                    <label for="f_status" class="form-label text-muted small fw-bold mb-1">Estado</label>
                    <select id="f_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Proceso">En Proceso</option>
                        <option value="Finalizado">Finalizado</option>
                    </select>
                </div>
                <div style="width: 160px;" class="d-flex align-items-end">
                    <button class="btn btn-primary w-100 mt-4" id="btnFilter">Filtrar DataGrid</button>
                </div>
            </div>
            
            <div class="table-custom-container">
                <table class="table-simple">
                    <thead>
                        <tr>
                            <th>DNI</th>
                            <th>Candidato</th>
                            <th>Estado Global</th>
                            <th>Fecha</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="adminTableBody">
                        <tr><td colspan="5" class="text-center py-4 text-muted">Cargando datos...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- VISTA: SOLICITUDES -->
        <div id="view-solicitudes" class="section-view">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>Vista extendida de solicitudes con historial de estados.
            </div>
        </div>

        <!-- VISTA: LOTE DETALLADO -->
        <div id="view-lote" class="section-view">
            <div class="table-custom-container">
                <h6 class="fw-bold mb-3">Lote Detallado por Servicio</h6>
                <p class="text-muted small">Carga rápida de estado por cada servicio individual.</p>
            </div>
        </div>

        <!-- VISTA: REPORTES -->
        <div id="view-reportes" class="section-view">
            <div class="table-custom-container">
                <h6 class="fw-bold mb-3">Reportes Globales</h6>
                <p class="text-muted small">Métricas y exportación general del sistema.</p>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="fas fa-tasks me-2" style="color:var(--teal)"></i>Gestión de Solicitud</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalContent"></div>
        </div>
    </div>
</div>

<!-- Carga del archivo JS propio -->
<!-- <script src="{{ asset('js/admin.js') }}"></script> -->
@endsection