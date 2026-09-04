<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración - SmarTalent')</title>

    <head>
    <!-- Script Anti-Parpadeo de seguridad -->
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        }
    </script>
    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body>

    <!-- Sidebar Fijo -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <img src="https://www.stgconsultoria.com/imagenes/logo1.png" alt="SmarTalent Group">
            <h6>Panel Administrador</h6>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-th-large"></i> Dashboard
            </a>
            <a href="{{ route('admin.requests') }}" class="{{ request()->routeIs('admin.requests') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Solicitudes
            </a>
            <a href="{{ route('admin.monthly-expedient') }}" class="{{ request()->routeIs('admin.monthly-expedient') ? 'active' : '' }}">
                <i class="fas fa-table"></i> Lote Detallado
            </a>
            <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                <i class="fas fa-chart-pie"></i> Reportes
            </a>
            <a href="{{ route('admin.clients') }}" class="{{ request()->routeIs('admin.clients') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Clientes
            </a>
            <a href="{{ route('admin.configuration') }}" class="{{ request()->routeIs('admin.configuration') ? 'active' : '' }}">
                <i class="fas fa-cog"></i> Configuración
            </a>
        </nav>
        <div class="sidebar-footer">
            <button id="btnLogout"><i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión</button>
        </div>
    </aside>

    <!-- Contenido Principal -->
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex align-items-center gap-3">
                <h5 id="pageTitle">
                    <i class="fas fa-shield-halved me-2" style="color:var(--teal)"></i>
                    @yield('page-title', 'Panel de Administración')
                </h5>
            </div>
            <div class="d-flex align-items-center gap-2">
                <!-- Reemplaza tu botón actual de modo oscuro en admin.blade.php por este -->
<button type="button" id="btnThemeToggle" class="dark-mode-btn d-flex align-items-center gap-2">
    <i id="themeIcon" class="fa-solid fa-moon"></i>
    <span id="themeText">Oscuro</span>
</button>
                <span class="badge-info">
                    <i class="fas fa-user-shield me-1"></i> Administrador
                </span>
            </div>
        </div>

        <div class="p-4 flex-grow-1">
            @yield('content')
        </div>
    </div>

    <!-- Modal General -->
    <div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="fas fa-tasks me-2" style="color:var(--teal)"></i>Gestión de Solicitud
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="modalContent"></div>
            </div>
        </div>
    </div>

    @stack('scripts')
</body>
</html>