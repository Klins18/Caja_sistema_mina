<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode([]);
    exit();
}

include 'includes/db.php';

$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$busqueda = $_GET['busqueda'] ?? '';

$where = "WHERE m.activo=1";
$params = [];

if($fecha_inicio) $where .= " AND m.fecha >= '$fecha_inicio'";
if($fecha_fin) $where .= " AND m.fecha <= '$fecha_fin'";
if($busqueda) $where .= " AND (m.factura LIKE '%$busqueda%' OR p.nombre LIKE '%$busqueda%')";

$query = "SELECT m.id_mov, m.fecha, p.nombre as proveedor, m.factura, d.producto, d.cantidad, d.unidad, d.Total, l.nombre_labor, m.tipo_entrada_salida, m.observacion
          FROM movcaja m
          LEFT JOIN proveedor p ON m.id_proveedor_proveedor=p.id_proveedor
          LEFT JOIN detallemov d ON m.id_mov=d.id_mov_MovCaja
          LEFT JOIN labor l ON m.id_labor_Labor=l.id_labor
          $where
          ORDER BY m.fecha DESC";

$result = $conn->query($query);
$data = [];
while($row = $result->fetch_assoc()){
    $data[] = $row;
}

echo json_encode($data);
