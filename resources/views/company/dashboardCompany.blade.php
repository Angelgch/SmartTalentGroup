@extends('layouts.app')

@section('title', 'Portal Cliente - SmarTalent')

@push('styles')
    <link href="{{ asset('css/cliente.css') }}" rel="stylesheet">
@endpush

@section('content')
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="{{ asset('assets/img/logo.png') }}" alt="Logo" class="bg-white rounded p-1 me-2" height="40">
                <span>Portal Cliente</span>
            </a>
            <button class="btn btn-sm btn-outline-light" id="btnLogout">Cerrar Sesión</button>
        </div>
    </nav>

    <!-- Contenido General -->
    <div class="container mt-4">
        <!-- Pestañas (Tabs) -->
        <ul class="nav nav-tabs" id="clientTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="new-req-tab" data-bs-toggle="tab" data-bs-target="#newReq" type="button" role="tab">Nueva Solicitud</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="tabHistory" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab">Seguimiento</button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-4 tab-content-custom bg-white shadow-sm rounded-bottom" id="clientTabsContent">
            
            <!-- Pestaña: Nueva Solicitud -->
            <div class="tab-pane fade show active" id="newReq" role="tabpanel">
                <form id="requestForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="dni" class="form-label">DNI (8 dígitos)</label>
                            <input type="text" class="form-control" id="dni" pattern="\d{8}" maxlength="8" placeholder="12345678" required>
                        </div>
                        <div class="col-md-6">
                            <label for="names" class="form-label">Nombres</label>
                            <input type="text" class="form-control" id="names" required>
                        </div>
                        <div class="col-md-6">
                            <label for="surnames" class="form-label">Apellidos</label>
                            <input type="text" class="form-control" id="surnames" required>
                        </div>
                        <div class="col-md-6">
                            <label for="email" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="email" placeholder="candidato@correo.com" required>
                        </div>
                        <div class="col-md-6">
                            <label for="phone" class="form-label">Teléfono</label>
                            <input type="number" class="form-control" id="phone" placeholder="987654321" required>
                        </div>
                    </div>

                    <hr class="my-4">

                    <h5 class="mb-3">Seleccione Servicios (Mínimo 1)</h5>
                    <div class="row g-2 mb-4" id="servicesCheckboxes">
                        <div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" value="Antecedentes Nacionales"> Antecedentes Nacionales</label></div>
                        <div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" value="Verificaciones Crediticias"> Verif. Crediticias</label></div>
                        <div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" value="Verificaciones Laborales"> Verif. Laborales</label></div>
                        <div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" value="Récord Laboral"> Récord Laboral</label></div>
                        <div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" value="Verificaciones Académicas"> Verif. Académicas</label></div>
                        <div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" value="Verificaciones Domiciliarias"> Verif. Domiciliarias</label></div>
                        <div class="col-md-4"><label class="form-check-label"><input type="checkbox" class="form-check-input me-1" value="Ficha RENIEC"> Ficha RENIEC</label></div>
                    </div>

                    <div class="mb-3">
                        <label for="observations" class="form-label">Observaciones</label>
                        <textarea class="form-control" id="observations" rows="3" placeholder="Información adicional relevante..."></textarea>
                    </div>

                    <button type="submit" class="btn btn-success px-4">Generar Solicitud</button>
                </form>
            </div>

            <!-- Pestaña: Seguimiento / Historial -->
            <div class="tab-pane fade" id="history" role="tabpanel">
                <div id="historyContainer">
                    <!-- Acordeón dinámico generado mediante JS -->
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/cliente.js') }}"></script>
@endpush