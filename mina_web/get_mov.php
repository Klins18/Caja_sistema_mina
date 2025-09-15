<?php
session_start();
include 'includes/db.php';

$id = $_GET['id'] ?? null;
if(!$id){
    echo json_encode(['error'=>'ID no proporcionado']);
    exit;
}

// Obtener movimiento
$stmt = $pdo->prepare("SELECT m.*, p.nombre as proveedor FROM movcaja m LEFT JOIN proveedor p ON p.id_proveedor = m.id_proveedor_proveedor WHERE m.id_mov=?");
$stmt->execute([$id]);
$mov = $stmt->fetch(PDO::FETCH_ASSOC);

if(!$mov){
    echo json_encode(['error'=>'Movimiento no encontrado']);
    exit;
}

// Datos básicos
$data = [
    'fecha'=>$mov['fecha'],
    'proveedor'=>$mov['proveedor'],
    'factura'=>$mov['factura'],
    'tipo_entrada_salida'=>$mov['tipo_entrada_salida'],
    'observacion'=>$mov['observacion'],
    'labor'=>$mov['id_labor_Labor'],
    'familia'=>$mov['id_Familia_Familia'] ?? '',
    'productos'=>[]
];

// Productos si es salida
if($mov['tipo_entrada_salida']==='Salida'){
    $stmtDet = $pdo->prepare("SELECT * FROM detallemov WHERE id_mov_MovCaja=?");
    $stmtDet->execute([$id]);
    $productos = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
    $data['productos'] = $productos;
}

echo json_encode($data);
?>
