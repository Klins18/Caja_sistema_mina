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
        $stmt = $pdo->prepare("SELECT id_deposito, fecha, monto, factura, concepto, id_proveedor_proveedor FROM depositopago WHERE factura LIKE :busqueda");
        $stmt->execute(['busqueda' => "%$busqueda%"]);
    } else {
        $stmt = $pdo->query("SELECT id_deposito, fecha, monto, factura, concepto, id_proveedor_proveedor FROM depositopago");
    }
    $registros = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error en la consulta: " . $e->getMessage());
}
?>

<h1 class="h2">Listado de Depósitos</h1>

<form class="mb-3" method="get">
    <div class="input-group">
        <input type="text" name="busqueda" class="form-control" placeholder="Buscar por factura" value="<?= htmlspecialchars($busqueda) ?>">
        <button class="btn btn-primary" type="submit">Buscar</button>
    </div>
</form>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>ID</th>
            <th>Fecha</th>
            <th>Monto</th>
            <th>Factura</th>
            <th>Concepto</th>
            <th>ID Proveedor</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($registros as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['id_deposito']) ?></td>
                <td><?= htmlspecialchars($row['fecha']) ?></td>
                <td><?= htmlspecialchars($row['monto']) ?></td>
                <td><?= htmlspecialchars($row['factura']) ?></td>
                <td><?= htmlspecialchars($row['concepto']) ?></td>
                <td><?= htmlspecialchars($row['id_proveedor_proveedor']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
