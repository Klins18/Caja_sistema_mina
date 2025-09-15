<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    exit('No autorizado');
}

include 'includes/db.php';

// Obtener último saldo
$saldo = $pdo->query("SELECT saldo FROM movcaja ORDER BY id_mov DESC LIMIT 1")->fetchColumn();
echo number_format($saldo, 2);
