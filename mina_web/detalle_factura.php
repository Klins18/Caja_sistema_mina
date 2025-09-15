<?php
include 'includes/db.php';

$id_mov = intval($_GET['id_mov']);

$stmt = $pdo->prepare("
    SELECT d.producto, d.cantidad, d.unidad, d.precio_unitario, d.subtotal, d.IGVMonto, d.Total, f.nombre AS familia
    FROM detallemov d
    LEFT JOIN familia f ON d.id_Familia_Familia = f.id_Familia
    WHERE d.id_mov_MovCaja = ?
");
$stmt->execute([$id_mov]);
$detalles = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode($detalles);
