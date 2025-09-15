<?php
include 'includes/db.php';

$id_mov = $_POST['id_mov'];

// Eliminar detalle si existe
$stmtDet = $pdo->prepare("DELETE FROM detallemov WHERE id_mov_MovCaja=?");
$stmtDet->execute([$id_mov]);

// Eliminar movcaja
$stmtMov = $pdo->prepare("DELETE FROM movcaja WHERE id_mov=?");
$stmtMov->execute([$id_mov]);

// Recalcular saldos
$movs = $pdo->query("SELECT * FROM movcaja ORDER BY id_mov")->fetchAll(PDO::FETCH_ASSOC);
$saldo=0;
foreach($movs as $m){
    $id=$m['id_mov'];
    $saldo = ($m['tipo_entrada_salida']=='Entrada')?$saldo+$m['monto']:$saldo-$m['monto'];
    $stmtSaldo=$pdo->prepare("UPDATE movcaja SET saldo=? WHERE id_mov=?");
    $stmtSaldo->execute([$saldo,$id]);
}

echo json_encode(['success'=>true,'nuevo_saldo'=>number_format($saldo,2)]);
?>
