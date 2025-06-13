<?php 
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar que el usuario esté logueado
if (!isset($_SESSION['usuario_id']) || empty($_SESSION['usuario_id'])) {
    header("Location: /app_login/");
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="<?= asset('build/js/app.js') ?>"></script>
    <link rel="shortcut icon" href="<?= asset('images/cit.png') ?>" type="image/x-icon">
    <link rel="stylesheet" href="<?= asset('build/styles.css') ?>">
    <title>Sistema de Gestión - <?= $_SESSION['nombre_completo'] ?? 'Usuario' ?></title>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarToggler" aria-controls="navbarToggler" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <a class="navbar-brand" href="/app_login/inicio">
                <img src="<?= asset('./images/cit.png') ?>" width="35px" alt="cit">
                Sistema de Aplicaciones
            </a>
            
            <div class="collapse navbar-collapse" id="navbarToggler">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0" style="margin: 0;">
                    
                    <!-- Inicio -->
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app_login/inicio">
                            <i class="bi bi-house-fill me-2"></i>Inicio
                        </a>
                    </li>

                    <!-- Gestión de Usuarios -->
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app_login/usuarios">
                            <i class="bi bi-person-add me-2"></i>Usuarios
                        </a>
                    </li>

                    <!-- Gestión de Aplicaciones -->
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app_login/aplicaciones">
                            <i class="bi bi-app me-2"></i>Aplicaciones
                        </a>
                    </li>

                    <!-- Gestión de Permisos -->
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app_login/permisos">
                            <i class="bi bi-lock me-2"></i>Permisos
                        </a>
                    </li>

                    <!-- Asignación de Permisos -->
                    <li class="nav-item">
                        <a class="nav-link" aria-current="page" href="/app_login/asignacionPermisos">
                            <i class="bi bi-pencil-square me-2"></i>Asignación de Permisos
                        </a>
                    </li>

                    <!-- Dropdown de Administración -->
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                            <i class="bi bi-gear me-2"></i>Administración
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark" id="dropdownRevision" style="margin: 0;">
                            <h6 class="dropdown-header">Configuración del Sistema</h6>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/app_login/reportes">
                                    <i class="ms-lg-0 ms-2 bi bi-file-earmark-text me-2"></i>Reportes
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/app_login/auditoria">
                                    <i class="ms-lg-0 ms-2 bi bi-clock-history me-2"></i>Auditoría
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item nav-link text-white" href="/app_login/configuracion">
                                    <i class="ms-lg-0 ms-2 bi bi-sliders me-2"></i>Configuración
                                </a>
                            </li>
                        </ul>
                    </div>

                </ul>

                <!-- Información del Usuario y Logout -->
                <div class="navbar-nav">
                    <div class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" data-bs-toggle="dropdown">
                            <?php if (isset($_SESSION['usuario_fotografia']) && !empty($_SESSION['usuario_fotografia'])): ?>
                                <img src="/app_login/public/<?= $_SESSION['usuario_fotografia'] ?>" 
                                     alt="Foto usuario" 
                                     class="rounded-circle me-2" 
                                     style="width: 30px; height: 30px; object-fit: cover;">
                            <?php else: ?>
                                <i class="bi bi-person-circle me-2"></i>
                            <?php endif; ?>
                            <?= $_SESSION['nombre_completo'] ?? 'Usuario' ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li><h6 class="dropdown-header">Mi Cuenta</h6></li>
                            <li>
                                <a class="dropdown-item" href="/app_login/perfil">
                                    <i class="bi bi-person me-2"></i>Mi Perfil
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="/app_login/cambiar-password">
                                    <i class="bi bi-key me-2"></i>Cambiar Contraseña
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="/app_login/logout">
                                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="progress fixed-bottom" style="height: 6px;">
        <div class="progress-bar progress-bar-animated bg-danger" id="bar" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
    </div>
    
    <div class="container-fluid pt-5 mb-4" style="min-height: 85vh">
        <?php echo $contenido; ?>
    </div>
    
    <div class="container-fluid">
        <div class="row justify-content-center text-center">
            <div class="col-12">
                <p style="font-size:xx-small; font-weight: bold;">
                    Sistema de Gestión de Aplicaciones - Comando de Informática y Tecnología, <?= date('Y') ?> &copy;<br>
                    <small class="text-muted">
                        Usuario: <?= $_SESSION['usuario_correo'] ?? '' ?> | 
                        Sesión iniciada: <?= date('d/m/Y H:i', $_SESSION['login_time'] ?? time()) ?>
                    </small>
                </p>
            </div>
        </div>
    </div>
</body>

</html>