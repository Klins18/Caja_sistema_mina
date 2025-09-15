<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';
include 'includes/header.php';

$busqueda = $_GET['busqueda'] ?? '';

try {
    if ($busqueda !== '') {
        $stmt = $pdo->prepare("SELECT id_alerta, tipo_alerta, descripcion, id_deposito_DepositoPago, id_mov_MovCaja FROM alerta WHERE tipo_alerta LIKE :busqueda");
        $stmt->execute(['busqueda' => "%$busqueda%"]);
    } else {
        $stmt = $pdo->query("SELECT id_alerta, tipo_alerta, descripcion, id_deposito_DepositoPago, id_mov_MovCaja FROM alerta");
    }
    $registros = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<h1 class="h2">Listado de Alertas</h1>

<form class="mb-3" method="get">
    <div class="input-group">
        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por tipo de alerta" value="<?= htmlspecialchars($busqueda) ?>">
        <button class="btn btn-primary" type="submit">Buscar</button>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Tipo de Alerta</th>
            <th>Descripción</th>
            <th>ID Depósito</th>
            <th>ID Movimiento</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id_alerta']) ?></td>
                <td><?= htmlspecialchars($row['tipo_alerta']) ?></td>
                <td><?= htmlspecialchars($row['descripcion']) ?></td>
                <td><?= htmlspecialchars($row['id_deposito_DepositoPago']) ?></td>
                <td><?= htmlspecialchars($row['id_mov_MovCaja']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
