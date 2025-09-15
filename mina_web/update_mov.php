<?php
include 'includes/db.php';

$id_mov = $_POST['id_mov'];
$fecha = $_POST['fecha'];
$proveedor = trim($_POST['proveedor']);
$factura = $_POST['factura'];
$tipo = $_POST['tipo'];
$observacion = $_POST['observacion'];
$labor = $_POST['labor'];
$familia = $_POST['familia'] ?? null;
$monto = $_POST['monto'] ?? 0;

// Actualizar proveedor
$stmtProv = $pdo->prepare("SELECT id_proveedor FROM proveedor WHERE nombre=?");
$stmtProv->execute([$proveedor]);
$id_proveedor = $stmtProv->fetchColumn();
if(!$id_proveedor && $proveedor != ''){
    $stmtInsProv = $pdo->prepare("INSERT INTO proveedor(nombre,RUC) VALUES (?,0)");
    $stmtInsProv->execute([$proveedor]);
    $id_proveedor = $pdo->lastInsertId();
}

// Actualizar movimiento
$stmt = $pdo->prepare("UPDATE movcaja SET fecha=?, factura=?, id_proveedor_proveedor=?, tipo_entrada_salida=?, observacion=?, id_labor_Labor=?, monto=?, id_Familia_Familia=? WHERE id_mov=?");
$stmt->execute([$fecha,$factura,$id_proveedor,$tipo,$observacion,$labor,$monto,$familia,$id_mov]);

// Recalcular saldos de todos los movimientos
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
