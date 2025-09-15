<?php
include 'db.php';
if(!isset($_GET['id'])) exit(json_encode(['success'=>false]));
$id = intval($_GET['id']);
$stmt = $pdo->prepare("SELECT d.*, f.nombre AS nombre_familia, l.nombre_labor
                       FROM detallemov d
                       LEFT JOIN familia f ON d.id_Familia_Familia=f.id_Familia
                       LEFT JOIN labor l ON d.id_Familia_Familia=l.id_labor
                       WHERE d.id_detallemov=?");
$stmt->execute([$id]);
$detalle = $stmt->fetch();
if($detalle) echo json_encode(['success'=>true,'detalle'=>$detalle]);
else echo json_encode(['success'=>false]);
?>
