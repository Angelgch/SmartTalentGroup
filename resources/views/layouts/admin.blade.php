<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración - SmarTalent')</title>

    <!-- Script Anti-Parpadeo (Tema Oscuro) -->
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        }
    </script>
    <!-- 1. Script Anti-Parpadeo (Tema Oscuro + Sidebar Colapsado) -->
    <script>
        // Aplicar Tema Oscuro
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        }
        // Aplicar Colapso de Sidebar inmediatamente
        if (localStorage.getItem("sidebar_collapsed") === "true") {
            document.documentElement.classList.add("sidebar-is-collapsed");
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="{{ session('sidebar_collapsed', false) ? 'sidebar-is-collapsed' : '' }}">

    <!-- TOPBAR SUPERIOR (ANCHO COMPLETO ESTILO YOUTUBE) -->
    <header class="topbar">
        <!-- Lado Izquierdo: Botón Menú + Logo -->
        <div class="topbar-left">
            <button type="button" id="btnToggleSidebar" class="btn-sidebar-toggle" title="Menú">
                <i class="fas fa-bars"></i>
            </button>
            <a href="{{ route('admin.dashboard') }}" class="topbar-brand">
                <img src="https://www.stgconsultoria.com/imagenes/logo1.png" alt="SmarTalent Group" class="topbar-logo">
            </a>
            <h5 id="pageTitle" class="page-title mb-0 ms-2">
                <i class="fas fa-shield-halved me-2" style="color:var(--teal)"></i>
                @yield('page-title', 'Panel de Administración')
            </h5>
        </div>

        <!-- Lado Derecho: Modo Oscuro + Perfil Dropdown -->
        <div class="topbar-right d-flex align-items-center gap-3">
            <button type="button" id="btnThemeToggle" class="dark-mode-btn d-flex align-items-center gap-2">
                <i id="themeIcon" class="fa-solid fa-moon"></i>
                <span id="themeText">Oscuro</span>
            </button>

            <div class="dropdown">
                <button class="btn btn-profile-dropdown dropdown-toggle d-flex align-items-center gap-2" 
                        type="button" 
                        id="userProfileDropdown" 
                        data-bs-toggle="dropdown" 
                        aria-expanded="false">
                    <i class="fa-solid fa-user-shield fs-5"></i>
                    <span class="fw-semibold">Administrador</span>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userProfileDropdown">
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.configuration') }}">
                            <i class="fa-solid fa-id-card text-muted"></i> Mi Perfil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.configuration') }}">
                            <i class="fa-solid fa-gear text-muted"></i> Configuración
                        </a>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <button type="button" id="btnLogout" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold w-100 border-0 bg-transparent">
                            <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- WRAPPER INFERIOR -->
    <div class="app-container">
        <!-- SIDEBAR (DEBAJO DEL NAVBAR) -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="fas fa-th-large"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="{{ route('admin.requests') }}" class="{{ request()->routeIs('admin.requests') ? 'active' : '' }}" title="Solicitudes">
                    <i class="fas fa-list"></i>
                    <span class="sidebar-text">Solicitudes</span>
                </a>
                <a href="{{ route('admin.monthly-expedient') }}" class="{{ request()->routeIs('admin.monthly-expedient') ? 'active' : '' }}" title="Lote Detallado">
                    <i class="fas fa-table"></i>
                    <span class="sidebar-text">Lote Detallado</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}" title="Reportes">
                    <i class="fas fa-chart-pie"></i>
                    <span class="sidebar-text">Reportes</span>
                </a>
                <a href="{{ route('admin.clients') }}" class="{{ request()->routeIs('admin.clients') ? 'active' : '' }}" title="Clientes">
                    <i class="fas fa-users"></i>
                    <span class="sidebar-text">Clientes</span>
                </a>
                <a href="{{ route('admin.configuration') }}" class="{{ request()->routeIs('admin.configuration') ? 'active' : '' }}" title="Configuración">
                    <i class="fas fa-cog"></i>
                    <span class="sidebar-text">Configuración</span>
                </a>
            </nav>
        </aside>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content" id="mainContent">
            <div class="p-4 flex-grow-1">
                @yield('content')
            </div>
        </main>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>