<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Definimos los items del menú
$menuItems = [
    'proveedores.php' => ['icon' => 'users', 'label' => 'Proveedores'],
    'labores.php' => ['icon' => 'briefcase', 'label' => 'Labores'],
    'familias.php' => ['icon' => 'layers', 'label' => 'Familias'],
    'facturas.php' => ['icon' => 'file', 'label' => 'Facturas'],
    'movcaja.php' => ['icon' => 'credit-card', 'label' => 'Movimiento de caja'],
    'reportes.php' => ['icon' => 'file-text', 'label' => 'Reportes'],
    'alertas.php' => ['icon' => 'alert-circle', 'label' => 'Alertas'],
    'depositos.php' => ['icon' => 'dollar-sign', 'label' => 'Depósitos']
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mina Web - Dashboard</title>
    <!-- Bootstrap CSS offline -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="assets/css/styles.css" rel="stylesheet">
    <!-- Feather Icons offline -->
    <script src="assets/js/feather.min.js"></script>
</head>
<body>
    <!-- Barra superior -->
    <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
        <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="index.php">Mina Web</a>
        <button class="navbar-toggler d-md-none collapsed" type="button" 
                data-bs-toggle="collapse" data-bs-target="#sidebarMenu" 
                aria-controls="sidebarMenu" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="navbar-nav ms-auto">
            <div class="nav-item text-nowrap">
                <a class="nav-link px-3 text-danger d-flex align-items-center" href="logout.php">
                    <i data-feather="power" class="me-1"></i> 
                    <strong>Cerrar sesión</strong>
                </a>
            </div>
        </div>
    </header>

    <!-- Contenedor principal -->
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar (colapsable) -->
            <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <?php foreach($menuItems as $link => $item): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="<?= $link ?>">
                                    <i data-feather="<?= $item['icon'] ?>"></i>
                                    <?= $item['label'] ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </nav>

            <!-- Área de contenido -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
