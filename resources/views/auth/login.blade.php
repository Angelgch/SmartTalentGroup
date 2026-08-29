<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SmarTalent Group - Sistema de Validación de Talento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- @vite(['resources/css/login.css']) -->
    @vite(['resources/css/login.css', 'resources/js/login.js'])
</head>
<body>

    <!-- Selector Flotante (Configuración Idioma / Tema) -->
    <div class="top-controls d-flex align-items-center gap-2">
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-globe me-1"></i> ES
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item active" href="#">Español (ES)</a></li>
                <li><a class="dropdown-item" href="#">English (EN)</a></li>
            </ul>
        </div>
        <div class="dropdown">
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                <i class="fa-solid fa-sun me-1"></i> Claro
            </button>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item active" href="#"><i class="fa-solid fa-sun me-2"></i>Claro</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-moon me-2"></i>Oscuro</a></li>
            </ul>
        </div>
    </div>

    <div class="login-wrapper">
        <!-- Columna Izquierda: Logo Grande y Slogan -->
        <div class="branding-section">
            <div class="branding-content text-center">
                
                <img src="{{ asset('images/logo.png') }}" alt="Smart Talent Group" class="brand-logo-large">
                <h2 class="slogan-title mb-4">CONECTA CON EL TALENTO HUMANO MEJOR CALIFICADO</h2>
        </div>
        </div>

        <!-- Columna Derecha: Formularios -->
        <div class="form-section">
            <div class="form-container">
                
                <!-- Selector de Pestañas -->
                <div class="form-toggle-nav mb-4">
                    <button type="button" class="toggle-btn active" id="tabLogin">Ingresar</button>
                    <button type="button" class="toggle-btn" id="tabCompany">Crear Cuenta</button>
                </div>

                <!-- 1. INICIO DE SESIÓN -->
                <div id="loginCard" class="auth-card form-animated show">
                    <h3 class="fw-bold mb-1 color-dark">Iniciar sesión</h3>
                    <p class="text-muted small mb-4">Accede con tus credenciales de Smart Talent</p>

                    <form id="loginForm">
                        <div class="mb-3 text-start">
                            <label class="form-label small fw-semibold">Correo electrónico</label>
                            <input type="email" class="form-control form-control-custom" id="loginEmail" placeholder="admin@smartalent.com" required>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label small fw-semibold">Contraseña</label>
                            <input type="password" class="form-control form-control-custom" id="loginPassword" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-teal w-100 py-2 mt-2 fw-semibold">Ingresar al sistema</button>
                    </form>

                    <div class="demo-box mt-4 p-3 rounded">
                        <div class="small fw-semibold text-muted mb-1">Datos de Prueba:</div>
                        <div class="small text-muted"><strong>Admin:</strong> admin@smartalent.com / 123456</div>
                        <div class="small text-muted"><strong>Cliente:</strong> cliente@cliente.com / 123456</div>
                    </div>
                </div>

                <!-- 2. REGISTRO DE EMPRESA -->
                <div id="companyCard" class="auth-card form-animated d-none">
                    <h3 class="fw-bold mb-1 color-dark">Registro de Empresa</h3>
                    <p class="text-muted small mb-3">Registra tu organización para solicitar evaluaciones</p>

                    <form id="companyForm">
                        <div class="mb-2 text-start">
                            <label class="form-label small fw-semibold">RUC de la Empresa</label>
                            <input type="text" class="form-control form-control-custom" maxlength="11" placeholder="20123456789" required>
                        </div>
                        <div class="mb-2 text-start">
                            <label class="form-label small fw-semibold">Razón Social</label>
                            <input type="text" class="form-control form-control-custom" placeholder="Nombre de la empresa" required>
                        </div>
                        <div class="row g-2 text-start">
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-semibold">Teléfono</label>
                                <input type="tel" class="form-control form-control-custom" placeholder="987654321" required>
                            </div>
                            <div class="col-6 mb-2">
                                <label class="form-label small fw-semibold">Correo Corporativo</label>
                                <input type="email" class="form-control form-control-custom" placeholder="contacto@empresa.com" required>
                            </div>
                        </div>
                        <div class="mb-3 text-start">
                            <label class="form-label small fw-semibold">Contraseña</label>
                            <input type="password" class="form-control form-control-custom" placeholder="••••••••" required>
                        </div>
                        <button type="submit" class="btn btn-teal w-100 py-2 fw-semibold">Registrar Empresa</button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/login.js']) -->
</body>
</html>