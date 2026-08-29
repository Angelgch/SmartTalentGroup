@extends('layouts.app')

@section('title', 'Panel de Administrador - SmarTalent')

@push('styles')
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="bg-white rounded p-1 me-2" height="30">
                <span>Admin Panel</span>
            </a>
            <button class="btn btn-sm btn-outline-light" id="btnLogout">Cerrar Sesión</button>
        </div>
    </nav>

    <!-- Contenido Principal -->
    <div class="container-fluid mt-4">
        <!-- Filtros y Acciones -->
        <div class="card mb-3 shadow-sm border-0">
            <div class="card-body row align-items-end g-3">
                <div class="col-md-3">
                    <label for="f_text" class="form-label font-weight-bold">Buscar (DNI/Nombres)</label>
                    <input type="text" id="f_text" class="form-control" placeholder="Ingrese DNI o nombre...">
                </div>
                <div class="col-md-2">
                    <label for="f_status" class="form-label font-weight-bold">Estado</label>
                    <select id="f_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="Pendiente">Pendiente</option>
                        <option value="En Proceso">En Proceso</option>
                        <option value="Finalizado">Finalizado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary w-100" id="btnFilter">Filtrar DataGrid</button>
                </div>
                <div class="col-md-2 offset-md-2">
                    <button class="btn btn-success w-100" id="btnExport">Exportar a Excel</button>
                </div>
            </div>
        </div>
        
        <!-- Tabla DataGrid -->
        <div class="table-responsive bg-white rounded border shadow-sm">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>DNI</th>
                        <th>Candidato</th>
                        <th>Estado Global</th>
                        <th>Fecha</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="adminTableBody">
                    <!-- Filas renderizadas mediante JS -->
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal de Gestión de Servicios -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gestión de Solicitud</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent">
                    <!-- Contenido dinámico del detalle -->
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/admin.js') }}"></script>
@endpush