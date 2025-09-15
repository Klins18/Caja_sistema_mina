<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['ok'=>false,'msg'=>'No autorizado']);
    exit();
}

include 'includes/db.php';

// Leer JSON
$data = json_decode(file_get_contents('php://input'), true);
$id = $data['id'];

// Marcar como inactivo (no se borra físicamente)
$stmt = $conn->prepare("UPDATE movcaja SET activo=0 WHERE id_mov=?");
$stmt->bind_param("i",$id);
$stmt->execute();

// Opcional: marcar detallemov también
$stmt2 = $conn->prepare("UPDATE detallemov SET activo=0 WHERE id_mov_MovCaja=?");
$stmt2->bind_param("i",$id);
$stmt2->execute();

echo json_encode(['ok'=>true]);
?>
