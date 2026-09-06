<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panel de Administración - SmarTalent')</title>

    <!-- Script Anti-Parpadeo (Tema Oscuro + Sidebar Colapsado) -->
    <script>
        if (localStorage.getItem("theme") === "dark") {
            document.documentElement.setAttribute("data-theme", "dark");
        }
        if (localStorage.getItem("sidebar_collapsed") === "true") {
            document.documentElement.classList.add("sidebar-is-collapsed");
        }
    </script>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Fuente OpenDyslexic para Accesibilidad -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/opendyslexic@1.0.3/opendyslexic-regular.css">

    @vite(['resources/css/admin.css', 'resources/js/admin.js'])
</head>
<body class="{{ session('sidebar_collapsed', false) ? 'sidebar-is-collapsed' : '' }}">

    <!-- TOPBAR SUPERIOR -->
    <header class="topbar">
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

        <div class="topbar-right d-flex align-items-center gap-3">
            <!-- BOTÓN MODO OSCURO -->
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
                        <a class="dropdown-item d-flex align-items-center gap-2 py-2" href="{{ route('admin.profile') }}">
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
                        <form method="POST" action="{{ route('logout') }}" class="m-0">
                            @csrf
                            <button type="submit" class="dropdown-item d-flex align-items-center gap-2 py-2 text-danger fw-semibold w-100 border-0 bg-transparent">
                                <i class="fa-solid fa-right-from-bracket"></i> Cerrar Sesión
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </header>

    <!-- WRAPPER INFERIOR -->
    <div class="app-container">
        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <nav class="sidebar-nav">
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" title="Dashboard">
                    <i class="fas fa-th-large"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
                <a href="{{ route('admin.requests') }}" class="{{ request()->routeIs('admin.requests') ? 'active' : '' }}" title="Solicitudes">
                    <i class="fas fa-list-alt"></i>
                    <span class="sidebar-text">Solicitudes</span>
                </a>
                <a href="{{ route('admin.batches') }}" class="{{ request()->routeIs('admin.batches') ? 'active' : '' }}" title="Gestión de Lotes">
                    <i class="fas fa-folder-open"></i>
                    <span class="sidebar-text">Gestión de Lotes</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="{{ request()->routeIs('admin.reports') ? 'active' : '' }}" title="Reportes">
                    <i class="fas fa-chart-pie"></i>
                    <span class="sidebar-text">Reportes</span>
                </a>
                <a href="{{ route('admin.users') }}" class="{{ request()->routeIs('admin.users') ? 'active' : '' }}" title="Usuarios">
                    <i class="fas fa-users-cog"></i>
                    <span class="sidebar-text">Usuarios</span>
                </a>
                <a href="{{ route('admin.configuration') }}" class="{{ request()->routeIs('admin.configuration') ? 'active' : '' }}" title="Configuración">
                    <i class="fas fa-cog"></i>
                    <span class="sidebar-text">Configuración</span>
                </a>
                <a href="{{ route('admin.profile') }}" class="{{ request()->routeIs('admin.profile') ? 'active' : '' }}" title="Mi Perfil">
                    <i class="fas fa-user-circle"></i>
                    <span class="sidebar-text">Mi Perfil</span>
                </a>

                <!-- Soporte fijado en el fondo del menú -->
                <a href="{{ route('admin.support') }}" 
                   class="{{ request()->routeIs('admin.support') ? 'active' : '' }} nav-item-bottom" 
                   title="Soporte">
                    <i class="fas fa-headset"></i>
                    <span class="sidebar-text">Soporte</span>
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

    <!-- WIDGET FLOTANTE DE ACCESIBILIDAD -->
    <div class="position-fixed bottom-0 end-0 p-3 z-3">
        <div class="dropup">
            <button class="btn btn-primary rounded-circle shadow-lg d-flex align-items-center justify-content-center" 
                    type="button" 
                    id="btnAccessibility" 
                    data-bs-toggle="dropdown" 
                    aria-expanded="false"
                    style="width: 48px; height: 48px;"
                    title="Opciones de Accesibilidad">
                <i class="fas fa-universal-access fs-5"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2 mb-2" aria-labelledby="btnAccessibility" style="min-width: 220px;">
                <li class="dropdown-header fw-bold text-uppercase fs-7">Tamaño de Texto</li>
                <li><button type="button" class="dropdown-item py-1" onclick="adjustFontSize(1)"><i class="fas fa-plus me-2"></i>Aumentar texto</button></li>
                <li><button type="button" class="dropdown-item py-1" onclick="adjustFontSize(-1)"><i class="fas fa-minus me-2"></i>Reducir texto</button></li>
                
                <li><hr class="dropdown-divider my-1"></li>
                <li class="dropdown-header fw-bold text-uppercase fs-7">Lectura y Espaciado</li>
                <li><button type="button" class="dropdown-item py-1" onclick="toggleDyslexicFont()"><i class="fas fa-font me-2"></i>Fuente Dislexia</button></li>
                <li><button type="button" class="dropdown-item py-1" onclick="toggleTextSpacing()"><i class="fas fa-text-width me-2"></i>Espaciado de Texto</button></li>
                
                <li><hr class="dropdown-divider my-1"></li>
                <li class="dropdown-header fw-bold text-uppercase fs-7">Visión y Color</li>
                <li><button type="button" class="dropdown-item py-1" onclick="toggleHighContrast()"><i class="fas fa-adjust me-2"></i>Alto Contraste</button></li>
                <li><button type="button" class="dropdown-item py-1" onclick="setDaltonism('deuteranopia')"><i class="fas fa-eye me-2"></i>Deuteranopía</button></li>
                <li><button type="button" class="dropdown-item py-1" onclick="setDaltonism('protanopia')"><i class="fas fa-eye me-2"></i>Protanopía</button></li>
                <li><button type="button" class="dropdown-item py-1" onclick="setDaltonism('grayscale')"><i class="fas fa-palette me-2"></i>Monocromático</button></li>
                
                <li><hr class="dropdown-divider my-1"></li>
                <li><button type="button" class="dropdown-item text-danger py-1" onclick="resetAccessibility()"><i class="fas fa-undo me-2"></i>Restablecer Todo</button></li>
            </ul>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>