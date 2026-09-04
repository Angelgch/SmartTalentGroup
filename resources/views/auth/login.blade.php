<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Smart Talent Group — Acceso</title>
    
    <!-- Fuentes y Fuentes de Íconos -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/login.css', 'resources/js/login.js'])
</head>
<body>

    <!-- Cuadro de Prueba Temporal (Fijo Superior Izquierda) -->
    <div class="demo-box-floating">
        <strong data-i18n="demo_title">Datos de Prueba:</strong><br>
        <strong>Admin:</strong> admin@gmail.com / 12345<br>
        <strong>Company:</strong> client@gmail.com / 12345
    </div>

    <!-- Controles Flotantes Superior Derecho (Idioma y Tema) -->
    <div class="top-controls">
        <button id="btnLangToggle" class="control-btn" type="button">
            <i class="fa-solid fa-globe"></i> <span id="langText">ES</span>
        </button>
        <button id="btnThemeToggle" class="control-btn" type="button">
            <i class="fa-solid fa-moon" id="themeIcon"></i> <span id="themeText">Oscuro</span>
        </button>
    </div>

    <div class="auth-layout">
        
        <!-- LADO IZQUIERDO: Branding -->
        <div class="auth-sidebar">
            <div class="branding-content">
                <h2 class="slogan-title" data-i18n="slogan">CONECTA CON EL TALENTO HUMANO MEJOR CALIFICADO</h2>
                <img src="{{ asset('images/logo.png') }}" alt="Smart Talent Group" class="brand-logo-large">
            </div>
        </div>

        <!-- LADO DERECHO: Formularios y Controles -->
        <div class="auth-content">
            <div class="auth-form-wrapper">
                
                <!-- Selector Píldora (Se mantiene fijo arriba) -->
                <div class="auth-toggle-pill">
                    <div class="pill-slider" id="pillSlider"></div>
                    <button type="button" id="btnTabLogin" class="pill-btn active" onclick="switchTab('login')">
                        <i class="fa-solid fa-right-to-bracket"></i> <span data-i18n="tab_login">Ingresar</span>
                    </button>
                    <button type="button" id="btnTabReg" class="pill-btn" onclick="switchTab('register')">
                        <i class="fa-solid fa-user-plus"></i> <span data-i18n="tab_register">Registrarse</span>
                    </button>
                </div>

                <!-- ÁREA DE FORMULARIOS (Solo esta sección cambia de altura/contenido) -->
                <div class="forms-container">
                    
                    <!-- 1. FORMULARIO DE INGRESO -->
                    <div id="loginBlock" class="form-block show">
                        <h2 data-i18n="login_title">Iniciar sesión</h2>
                        <p class="subtitle" data-i18n="login_sub">Accede con tus credenciales de Smart Talent</p>
                        
                        <div id="loginError" class="alert-error"></div>

                        <form id="loginForm">
                            <div class="form-group">
                                <label class="form-label" data-i18n="email_label">Correo electrónico</label>
                                <input type="email" id="loginEmail" class="form-input" placeholder="correo@empresa.com" required>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label" data-i18n="pwd_label">Contraseña</label>
                                <div class="input-wrapper">
                                    <input type="password" id="loginPassword" class="form-input" placeholder="••••••••" required>
                                    <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('loginPassword')">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" data-i18n="login_btn">Ingresar al sistema</button>
                        </form>
                    </div>

                    <!-- 2. FORMULARIO DE REGISTRO -->
                    <div id="registerBlock" class="form-block d-none">
                        <h2 data-i18n="reg_title">Crear Cuenta</h2>
                        <p class="subtitle" data-i18n="reg_sub">Regístrate para solicitar evaluaciones corporativas</p>
                        
                        <div id="registerError" class="alert-error"></div>
                        <div id="registerSuccess" class="alert-success" data-i18n="reg_success">¡Registro Exitoso! Redirigiendo...</div>

                        <form id="registerForm">
                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label">RUC (Opcional)</label>
                                    <input type="text" id="regRuc" class="form-input" placeholder="20123456789">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">DNI</label>
                                    <input type="text" id="regDni" class="form-input" placeholder="70000000" maxlength="8" required>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="form-label" data-i18n="name_label">Nombre completo</label>
                                <input type="text" id="regName" class="form-input" placeholder="Ej. Juan Pérez" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" data-i18n="email_label">Correo electrónico</label>
                                <input type="email" id="regEmail" class="form-input" placeholder="correo@empresa.com" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label" data-i18n="phone_label">Teléfono</label>
                                <input type="tel" id="regPhone" class="form-input" placeholder="999 999 999" required>
                            </div>

                            <div class="form-grid-2">
                                <div class="form-group">
                                    <label class="form-label" data-i18n="pwd_label">Contraseña</label>
                                    <div class="input-wrapper">
                                        <input type="password" id="regPassword" class="form-input" placeholder="••••••••" required>
                                        <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('regPassword')">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label" data-i18n="confirm_pwd">Confirmar</label>
                                    <div class="input-wrapper">
                                        <input type="password" id="regPasswordConfirm" class="form-input" placeholder="••••••••" required>
                                        <button type="button" class="pwd-toggle" onclick="togglePasswordVisibility('regPasswordConfirm')">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary" data-i18n="reg_btn">Registrarse</button>
                        </form>
                    </div>

                </div>

            </div>
        </div>

    </div>

</body>
</html>