<?php
session_start();
if(!isset($_SESSION['usuario'])){
    header("Location: login.php");
    exit();
}

include 'includes/db.php';
include 'includes/header.php';

$mensaje = '';
$error = '';
$editando_id = isset($_GET['editar']) ? intval($_GET['editar']) : 0;

// ELIMINAR PROVEEDOR
if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    $stmtCheck = $pdo->prepare("SELECT COUNT(*) FROM movcaja WHERE id_proveedor_proveedor = ?");
    $stmtCheck->execute([$id]);
    $enUso = $stmtCheck->fetchColumn();

    if($enUso > 0){
        $error = "No se puede eliminar este proveedor porque está asociado a un movimiento.";
    } else {
        $stmtDel = $pdo->prepare("DELETE FROM proveedor WHERE id_proveedor = ?");
        $stmtDel->execute([$id]);
        $mensaje = "Proveedor eliminado correctamente.";
    }
}

// GUARDAR EDICIÓN
if(isset($_POST['guardar_edicion'])){
    $id = intval($_POST['id_proveedor']);
    $nombre = trim($_POST['nombre']);
    $ruc = trim($_POST['ruc']);

    if($nombre == ''){
        $error = "El nombre no puede estar vacío.";
    } else {
        $stmt = $pdo->prepare("UPDATE proveedor SET nombre = ?, RUC = ? WHERE id_proveedor = ?");
        $stmt->execute([$nombre, $ruc, $id]);

        // ✅ Redirigir sin usar header()
        echo "<script>
                window.location.href = 'proveedores.php?msg=actualizado';
              </script>";
        exit;
    }
}

// AGREGAR NUEVO PROVEEDOR
if(isset($_POST['agregar'])){
    $nombre = trim($_POST['nombre_new']);
    $ruc = trim($_POST['ruc_new']);

    if($nombre == ''){
        $error = "El nombre no puede estar vacío.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO proveedor (nombre, RUC) VALUES (?, ?)");
        $stmt->execute([$nombre, $ruc]);
        $mensaje = "Proveedor agregado correctamente.";
    }
}

// OBTENER PROVEEDORES
$proveedores = $pdo->query("SELECT * FROM proveedor ORDER BY nombre ASC")->fetchAll();
?>

<h1 class="h2">Listado de Proveedores</h1>

<?php if(isset($_GET['msg']) && $_GET['msg'] == 'actualizado'): ?>
    <div class="alert alert-success">Proveedor actualizado correctamente.</div>
<?php endif; ?>
<?php if($mensaje): ?>
    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>
<?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>

<h4>Agregar Nuevo Proveedor</h4>
<form method="post" class="mb-3 row g-2">
    <div class="col-md-5">
        <input type="text" name="nombre_new" class="form-control" placeholder="Nombre" required>
    </div>
    <div class="col-md-3">
        <input type="text" name="ruc_new" class="form-control" placeholder="RUC">
    </div>
    <div class="col-md-2">
        <button type="submit" name="agregar" class="btn btn-success">Agregar</button>
    </div>
</form>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>RUC</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($proveedores as $prov): ?>
        <tr>
            <?php if($editando_id == $prov['id_proveedor']): ?>
                <!-- FILA EN MODO EDICIÓN -->
                <form method="post">
                    <td>
                        <input type="hidden" name="id_proveedor" value="<?= $prov['id_proveedor'] ?>">
                        <input type="text" name="nombre" value="<?= htmlspecialchars($prov['nombre']) ?>" class="form-control" required>
                    </td>
                    <td>
                        <input type="text" name="ruc" value="<?= htmlspecialchars($prov['RUC']) ?>" class="form-control">
                    </td>
                    <td>
                        <button type="submit" name="guardar_edicion" class="btn btn-primary btn-sm">Guardar</button>
                        <a href="proveedores.php" class="btn btn-secondary btn-sm">Cancelar</a>
                    </td>
                </form>
            <?php else: ?>
                <!-- FILA NORMAL -->
                <td><?= htmlspecialchars($prov['nombre']) ?></td>
                <td><?= htmlspecialchars($prov['RUC']) ?></td>
                <td>
                    <a href="proveedores.php?editar=<?= $prov['id_proveedor'] ?>" class="btn btn-warning btn-sm">Editar</a>
                    <a href="proveedores.php?eliminar=<?= $prov['id_proveedor'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este proveedor?')">Eliminar</a>
                </td>
            <?php endif; ?>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php include 'includes/footer.php'; ?>
