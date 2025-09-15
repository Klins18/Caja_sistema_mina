<?php
function recalcularSaldos($pdo){
    $movimientos = $pdo->query("SELECT id_mov, tipo_entrada_salida, monto FROM movcaja ORDER BY fecha, id_mov")->fetchAll(PDO::FETCH_ASSOC);

    $saldo = 0;
    foreach($movimientos as $mov){
        if($mov['tipo_entrada_salida'] == 'Entrada'){
            $saldo += $mov['monto'];
        } else {
            $saldo -= $mov['monto'];
        }
        $stmt = $pdo->prepare("UPDATE movcaja SET saldo = ? WHERE id_mov = ?");
        $stmt->execute([$saldo, $mov['id_mov']]);
    }
}
