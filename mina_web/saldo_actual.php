<?php
include 'includes/db.php';

$saldo = $pdo->query("
    SELECT SUM(CASE WHEN tipo_entrada_salida='Entrada' THEN monto ELSE -monto END) AS saldo
    FROM movcaja
")->fetchColumn();

echo number_format($saldo,2);
?>
