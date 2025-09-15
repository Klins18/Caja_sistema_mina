<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';
include 'includes/header.php';

// Obtener lista de proveedores y labores
$proveedores = $pdo->query("SELECT id_proveedor, nombre FROM proveedor")->fetchAll();
$labores = $pdo->query("SELECT id_labor, nombre_labor FROM labor")->fetchAll();

$error = '';
$mensaje = '';

if(isset($_POST['guardar_movimiento'])){
    $fecha = $_POST['fecha'];
    $nombre_proveedor = trim($_POST['proveedor']);
    $factura = $_POST['factura'];
    $tipo = $_POST['tipo_movimiento'];
    $observacion = $_POST['observacion'] ?? '';
    $id_labor = intval($_POST['labor']);

    $productos = $_POST['producto'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];
    $unidades = $_POST['unidad'] ?? [];
    $precios = $_POST['precio'] ?? [];
    $familiasProd = $_POST['familiaProd'] ?? [];
    $monto_entrada = floatval($_POST['monto_entrada'] ?? 0);
    $familia_entrada = trim($_POST['familia_entrada'] ?? '');

    // Validación: salida sin descripción
    if($tipo == 'Salida'){
        $hayDescripcion = false;
        foreach($productos as $desc){
            if(trim($desc) != ''){
                $hayDescripcion = true;
                break;
            }
        }
        if(!$hayDescripcion){
            $error = "No puede guardar un movimiento de salida sin ninguna descripción de producto.";
        }
    }

    // Validación: familia de entrada
    if($tipo=='Entrada' && $familia_entrada==''){
        $error = "Debe ingresar la familia para la entrada.";
    }

    if(!$error){
        // Verificar o crear proveedor
        $stmtProv = $pdo->prepare("SELECT id_proveedor FROM proveedor WHERE nombre = ?");
        $stmtProv->execute([$nombre_proveedor]);
        $id_proveedor = $stmtProv->fetchColumn();
        if(!$id_proveedor && $nombre_proveedor != ''){
            $stmtInsProv = $pdo->prepare("INSERT INTO proveedor (nombre, RUC) VALUES (?, 0)");
            $stmtInsProv->execute([$nombre_proveedor]);
            $id_proveedor = $pdo->lastInsertId();
        }

        // Insertar movimiento
        $stmtMov = $pdo->prepare("INSERT INTO movcaja (fecha, factura, monto, tipo_entrada_salida, observacion, id_proveedor_proveedor, id_labor_Labor, id_Familia_Familia) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmtMov->execute([
            $fecha,
            $factura,
            0, // se actualizará luego
            $tipo,
            $observacion,
            $id_proveedor,
            $id_labor,
            null
        ]);
        $id_mov = $pdo->lastInsertId();

        $total_mov = 0;

        if($tipo == 'Entrada'){
            // Familia para entrada
            $stmtFam = $pdo->prepare("SELECT id_Familia FROM familia WHERE nombre = ?");
            $stmtFam->execute([$familia_entrada]);
            $id_familia = $stmtFam->fetchColumn();
            if(!$id_familia){
                $stmtInsFam = $pdo->prepare("INSERT INTO familia (nombre) VALUES (?)");
                $stmtInsFam->execute([$familia_entrada]);
                $id_familia = $pdo->lastInsertId();
            }

            $total_mov = $monto_entrada;

            // Actualizar el movimiento con monto y familia
            $stmtUpdate = $pdo->prepare("UPDATE movcaja SET monto = ?, id_Familia_Familia = ? WHERE id_mov = ?");
            $stmtUpdate->execute([$total_mov, $id_familia, $id_mov]);

        } else {
            // Salidas con productos
            foreach($productos as $i => $desc){
                if(trim($desc) == '') continue;

                $cantidad = floatval($cantidades[$i]);
                $unidad = $unidades[$i];
                $precio_total = floatval($precios[$i]);
                $familia_nombre = trim($familiasProd[$i]);

                if($familia_nombre == ''){
                    $error = "Debe ingresar una familia para el producto: $desc";
                    break;
                }

                // Verificar o crear familia
                $stmtFam = $pdo->prepare("SELECT id_Familia FROM familia WHERE nombre = ?");
                $stmtFam->execute([$familia_nombre]);
                $id_familia_prod = $stmtFam->fetchColumn();
                if(!$id_familia_prod){
                    $stmtInsFam = $pdo->prepare("INSERT INTO familia (nombre) VALUES (?)");
                    $stmtInsFam->execute([$familia_nombre]);
                    $id_familia_prod = $pdo->lastInsertId();
                }

                $IGV = round($precio_total * 18 / 118, 2);
                $subtotal = round($precio_total - $IGV, 2);
                $total_linea = $precio_total;

                $stmtDet = $pdo->prepare("INSERT INTO detallemov (cantidad, producto, unidad, precio_unitario, subtotal, IGVMonto, Total, id_mov_MovCaja, id_Familia_Familia) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                $stmtDet->execute([$cantidad, $desc, $unidad, $precio_total, $subtotal, $IGV, $total_linea, $id_mov, $id_familia_prod]);

                $total_mov += $total_linea;
            }

            if(!$error){
                // Actualizar monto del movimiento
                $stmtUpdate = $pdo->prepare("UPDATE movcaja SET monto = ? WHERE id_mov = ?");
                $stmtUpdate->execute([$total_mov, $id_mov]);
            }
        }

        // --- Recalcular saldos de todos los movimientos ---
        $movs = $pdo->query("SELECT id_mov, tipo_entrada_salida, monto FROM movcaja ORDER BY fecha, id_mov")->fetchAll(PDO::FETCH_ASSOC);
        $saldo = 0;
        $stmtSaldoUpdate = $pdo->prepare("UPDATE movcaja SET saldo = ? WHERE id_mov = ?");
        foreach($movs as $m){
            $monto = $m['tipo_entrada_salida']=='Entrada' ? $m['monto'] : -$m['monto'];
            $saldo += $monto;
            $stmtSaldoUpdate->execute([$saldo, $m['id_mov']]);
        }

        $mensaje = "Movimiento registrado correctamente. Nuevo saldo de caja: S/ $saldo";
    }
}

?>

<h1 class="h2">Registrar Movimiento de Caja</h1>

<?php if($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
<?php if($mensaje): ?>
    <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<form method="post" id="formMovimiento">
    <div class="row mb-3">
        <div class="col-md-3">
            <label>Fecha</label>
            <input type="date" name="fecha" class="form-control" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col-md-3">
            <label>Proveedor / Tienda</label>
            <input list="listaProveedores" name="proveedor" id="proveedorInput" class="form-control" required>
            <datalist id="listaProveedores">
                <?php foreach($proveedores as $p): ?>
                    <option value="<?= htmlspecialchars($p['nombre']) ?>"></option>
                <?php endforeach; ?>
            </datalist>
        </div>
        <div class="col-md-3">
            <label>Factura / Boleta</label>
            <input type="text" name="factura" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Tipo de Movimiento</label>
            <select name="tipo_movimiento" class="form-control" id="tipoMovimiento" required>
                <option value="Salida">Salida</option>
                <option value="Entrada">Entrada</option>
            </select>
        </div>
    </div>

    <div class="row mb-3" id="divMontoEntrada" style="display:none;">
        <div class="col-md-3">
            <label>Monto Entrada</label>
            <input type="number" name="monto_entrada" class="form-control" min="0" step="0.01">
        </div>
    </div>

    <div class="row mb-3" id="divFamiliaEntrada" style="display:none;">
        <div class="col-md-3">
            <label>Familia</label>
            <input type="text" name="familia_entrada" id="familiaEntrada" class="form-control" placeholder="Escriba la familia">
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-md-3">
            <label>Labor</label>
            <select name="labor" class="form-control" required>
                <option value="">Seleccionar</option>
                <?php foreach($labores as $l): ?>
                    <option value="<?= $l['id_labor'] ?>"><?= htmlspecialchars($l['nombre_labor']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-9">
            <label>Observación</label>
            <input type="text" name="observacion" class="form-control">
        </div>
    </div>

    <div id="productosContainer">
        <hr>
        <h4>Productos / Detalle de Compra (solo para Salida)</h4>
        <div class="table-responsive">
            <table class="table table-bordered" id="tablaProductos">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Precio Unitario</th>
                        <th>Familia</th>
                        <th>Subtotal</th>
                        <th>IGV</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        <button type="button" class="btn btn-secondary mb-3" id="btnAgregarProducto">Agregar Producto</button>
    </div>

    <button type="submit" name="guardar_movimiento" class="btn btn-primary">Guardar Movimiento</button>
</form>

<script>
const tipoMovimientoSelect = document.getElementById('tipoMovimiento');
const divMontoEntrada = document.getElementById('divMontoEntrada');
const divFamiliaEntrada = document.getElementById('divFamiliaEntrada');
const productosContainer = document.getElementById('productosContainer');

tipoMovimientoSelect.addEventListener('change', ()=>{
    if(tipoMovimientoSelect.value === 'Entrada'){
        divMontoEntrada.style.display = 'block';
        divFamiliaEntrada.style.display = 'block';
        productosContainer.style.display = 'none';
    } else {
        divMontoEntrada.style.display = 'none';
        divFamiliaEntrada.style.display = 'none';
        productosContainer.style.display = 'block';
    }
});

// Función para agregar filas dinámicas
function agregarFilaProducto(){
    const tbody = document.querySelector('#tablaProductos tbody');
    const row = document.createElement('tr');
    row.innerHTML = `
        <td><input type="text" name="producto[]" class="form-control productoInput"></td>
        <td><input type="number" name="cantidad[]" class="form-control cantidadInput" min="0" step="0.01"></td>
        <td><input type="text" name="unidad[]" class="form-control"></td>
        <td><input type="number" name="precio[]" class="form-control precioInput" min="0" step="0.01"></td>
        <td><input type="text" name="familiaProd[]" class="form-control familiaInput"></td>
        <td class="subtotal">0.00</td>
        <td class="igv">0.00</td>
        <td class="total">0.00</td>
        <td><button type="button" class="btn btn-danger btnEliminar">Eliminar</button></td>
    `;
    tbody.appendChild(row);

    const precioInput = row.querySelector('.precioInput');
    const cantidadInput = row.querySelector('.cantidadInput');

    function recalcular(){
        let total_linea = parseFloat(precioInput.value) || 0;
        let cantidad = parseFloat(cantidadInput.value) || 0;
        total_linea = total_linea * cantidad;
        let igv = 0;
        let subtotal = total_linea;

        if(tipoMovimientoSelect.value === 'Salida'){
            igv = +(total_linea * 18 / 118).toFixed(2);
            subtotal = +(total_linea - igv).toFixed(2);
        }

        row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
        row.querySelector('.igv').textContent = igv.toFixed(2);
        row.querySelector('.total').textContent = total_linea.toFixed(2);
    }

    precioInput.addEventListener('input', recalcular);
    cantidadInput.addEventListener('input', recalcular);
    tipoMovimientoSelect.addEventListener('change', recalcular);

    row.querySelector('.btnEliminar').addEventListener('click', ()=> {
        row.remove();
    });

    recalcular();
}

document.getElementById('btnAgregarProducto').addEventListener('click', agregarFilaProducto);
agregarFilaProducto();
</script>

<?php include 'includes/footer.php'; ?>
