<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include 'includes/header.php';
?>

<!-- Contenido principal -->
<h1 class="h2">Bienvenido al Sistema de Gestión de Mina Web</h1>
<p class="lead">Selecciona una opción en el menú lateral para comenzar.</p>

<?php include 'includes/footer.php'; ?>
