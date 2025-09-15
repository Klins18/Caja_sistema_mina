<?php
session_start();
if(!isset($_SESSION['usuario'])){
    exit('Acceso denegado');
}

include 'includes/db.php';

$id_mov = intval($_GET['id']);
if(!$id_mov){
    exit('ID no válido');
}

// Obtener datos del movimiento
$stmt = $pdo->prepare("SELECT * FROM movcaja WHERE id_mov = ?");
$stmt->execute([$id_mov]);
$mov = $stmt->fetch();

if(!$mov){
    exit('Movimiento no encontrado');
}

// Obtener proveedores y labores
$proveedores = $pdo->query("SELECT id_proveedor, nombre FROM proveedor")->fetchAll();
$labores = $pdo->query("SELECT id_labor, nombre_labor FROM labor")->fetchAll();
?>

<form id="formEditarMov">
    <input type="hidden" name="id_mov" value="<?= $mov['id_mov'] ?>">

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Fecha</label>
            <input type="date" name="fecha" class="form-control" value="<?= $mov['fecha'] ?>" required>
        </div>
        <div class="col-md-4">
            <label>Proveedor</label>
            <select name="id_proveedor" class="form-control" required>
                <option value="">Seleccionar</option>
                <?php foreach($proveedores as $p): ?>
                    <option value="<?= $p['id_proveedor'] ?>" <?= $p['id_proveedor']==$mov['id_proveedor_proveedor']?'selected':'' ?>><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Factura</label>
            <input type="text" name="factura" class="form-control" value="<?= htmlspecialchars($mov['factura']) ?>" required>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-4">
            <label>Tipo</label>
            <select name="tipo" class="form-control">
                <option value="Entrada" <?= $mov['tipo_entrada_salida']=='Entrada'?'selected':'' ?>>Entrada</option>
                <option value="Salida" <?= $mov['tipo_entrada_salida']=='Salida'?'selected':'' ?>>Salida</option>
            </select>
        </div>
        <div class="col-md-4">
            <label>Labor</label>
            <select name="id_labor" class="form-control">
                <option value="">Seleccionar</option>
                <?php foreach($labores as $l): ?>
                    <option value="<?= $l['id_labor'] ?>" <?= $l['id_labor']==$mov['id_labor_Labor']?'selected':'' ?>><?= htmlspecialchars($l['nombre_labor']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label>Monto</label>
            <input type="number" step="0.01" name="monto" class="form-control" value="<?= $mov['monto'] ?>">
        </div>
    </div>

    <div class="mb-3">
        <label>Observación</label>
        <input type="text" name="observacion" class="form-control" value="<?= htmlspecialchars($mov['observacion']) ?>">
    </div>
</form>
