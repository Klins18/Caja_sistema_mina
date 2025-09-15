<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';
include 'includes/header.php';

// Obtener filtros
$fecha_inicio = $_GET['fecha_inicio'] ?? '';
$fecha_fin = $_GET['fecha_fin'] ?? '';
$buscar = $_GET['buscar'] ?? '';

// Consulta de movimientos
$sql = "SELECT m.*, p.nombre as proveedor 
        FROM movcaja m 
        LEFT JOIN proveedor p ON p.id_proveedor = m.id_proveedor_proveedor 
        WHERE 1 ";

$params = [];

if($fecha_inicio && $fecha_fin){
    $sql .= " AND m.fecha BETWEEN ? AND ? ";
    $params[] = $fecha_inicio;
    $params[] = $fecha_fin;
}

if($buscar){
    $sql .= " AND (m.factura LIKE ? OR p.nombre LIKE ?) ";
    $params[] = "%$buscar%";
    $params[] = "%$buscar%";
}

$sql .= " ORDER BY m.fecha DESC, m.id_mov DESC";

$movimientos = $pdo->prepare($sql);
$movimientos->execute($params);
$movimientos = $movimientos->fetchAll(PDO::FETCH_ASSOC);

// Obtener saldo actual
$saldo_actual = $pdo->query("SELECT saldo FROM movcaja ORDER BY id_mov DESC LIMIT 1")->fetchColumn();

// Obtener labores y familias para los selects
$labores = $pdo->query("SELECT id_labor, nombre_labor FROM labor")->fetchAll(PDO::FETCH_ASSOC);
$familias = $pdo->query("SELECT id_Familia, nombre FROM familia")->fetchAll(PDO::FETCH_ASSOC);
?>

<h1 class="h2">Reportes de Movimientos</h1>

<!-- Filtros -->
<form method="get" class="mb-3 d-flex flex-wrap gap-2">
    <input type="date" name="fecha_inicio" class="form-control" value="<?= htmlspecialchars($fecha_inicio) ?>">
    <input type="date" name="fecha_fin" class="form-control" value="<?= htmlspecialchars($fecha_fin) ?>">
    <input type="text" name="buscar" class="form-control" placeholder="Factura o Proveedor" value="<?= htmlspecialchars($buscar) ?>">
    <button class="btn btn-primary">Filtrar</button>
</form>

<!-- Saldo Caja -->
<div id="saldoCaja" class="mb-3">
    <div id="valorSaldo"><?= number_format($saldo_actual,2) ?></div>
    <button id="toggleSaldo"><i data-feather="eye"></i></button>
</div>

<!-- Tabla -->
<div class="table-responsive">
<table class="table table-bordered" id="tablaReportes">
    <thead>
        <tr>
            <th>Fecha</th>
            <th>Tipo</th>
            <th>Proveedor</th>
            <th>Factura</th>
            <th>Cantidad</th>
            <th>Unidad</th>
            <th>Descripción</th>
            <th>Precio Unitario</th>
            <th>Monto</th>            
            <th>Labor</th>
            <th>Familia</th>
            <th>Total</th>
            <th>Observaciones</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach($movimientos as $mov):
            // Obtener detalles de productos si es salida
            $detalles = [];
            if($mov['tipo_entrada_salida'] === 'Salida'){
                $stmtDet = $pdo->prepare("SELECT * FROM detallemov WHERE id_mov_MovCaja=?");
                $stmtDet->execute([$mov['id_mov']]);
                $detalles = $stmtDet->fetchAll(PDO::FETCH_ASSOC);
            }
        ?>
            <?php if($mov['tipo_entrada_salida']==='Entrada'): ?>
                <tr data-id="<?= $mov['id_mov'] ?>" class="entrada-row" style="background-color:#e6f7e6;">
                    <td class="fechaCell"><?= $mov['fecha'] ?></td>
                    <td class="tipoCell"><?= $mov['tipo_entrada_salida'] ?></td>
                    <td class="proveedorCell"><?= htmlspecialchars($mov['proveedor']) ?></td>
                    <td class="facturaCell"><?= htmlspecialchars($mov['factura']) ?></td>
                    <td class="cantidadCell">-</td>
                    <td class="unidadCell">-</td>
                    <td class="descripcionCell">-</td>
                    <td class="precioUnitCell">-</td>
                    <td class="montoCell"><?= number_format($mov['monto'],2) ?></td>
                    <td class="laborCell"><?= $mov['id_labor_Labor'] ?></td>
                    <td class="familiaCell"><?= $mov['id_Familia_Familia'] ?? '' ?></td>
                    
                    
                    
                    
                    <td class="totalCell"><?= number_format($mov['monto'],2) ?></td>
                    <td class="observacionCell"><?= htmlspecialchars($mov['observacion']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info btnEditar">Editar</button>
                        <button class="btn btn-sm btn-danger btnEliminar">Eliminar</button>
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach($detalles as $det): ?>
                <tr data-id="<?= $mov['id_mov'] ?>" class="salida-row">
                    <td class="fechaCell"><?= $mov['fecha'] ?></td>
                    <td class="tipoCell"><?= $mov['tipo_entrada_salida'] ?></td>
                    <td class="proveedorCell"><?= htmlspecialchars($mov['proveedor']) ?></td>
                    <td class="facturaCell"><?= htmlspecialchars($mov['factura']) ?></td>
                    <td class="cantidadCell"><?= $det['cantidad'] ?></td>
                    <td class="unidadCell"><?= htmlspecialchars($det['unidad']) ?></td>
                    <td class="descripcionCell"><?= htmlspecialchars($det['producto']) ?></td>
                    <td class="precioUnitCell"><?= number_format($det['precio_unitario'],2) ?></td>
                    <td class="montoCell"><?= number_format($det['Total'],2) ?></td>
                    <td class="laborCell"><?= $mov['id_labor_Labor'] ?></td>
                    <td class="familiaCell"><?= $det['id_Familia_Familia'] ?></td>
                    
                    
                    
                    
                    <td class="totalCell"><?= number_format($det['Total'],2) ?></td>
                    <td class="observacionCell"><?= htmlspecialchars($mov['observacion']) ?></td>
                    <td>
                        <button class="btn btn-sm btn-info btnEditar">Editar</button>
                        <button class="btn btn-sm btn-danger btnEliminar">Eliminar</button>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    </tbody>
</table>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="editarModal" tabindex="-1" aria-hidden="true">
<div class="modal-dialog modal-xl">
<form id="formEditar">
<div class="modal-content">
    <div class="modal-header">
        <h5 class="modal-title">Editar Movimiento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
        <input type="hidden" name="id_mov" id="editIdMov">
        <div class="row mb-2">
            <div class="col-md-3"><label>Fecha</label><input type="date" class="form-control" name="fecha" id="editFecha"></div>
            <div class="col-md-3"><label>Proveedor</label><input type="text" class="form-control" name="proveedor" id="editProveedor"></div>
            <div class="col-md-3"><label>Factura</label><input type="text" class="form-control" name="factura" id="editFactura"></div>
            <div class="col-md-3"><label>Tipo</label>
                <select name="tipo" id="editTipo" class="form-control">
                    <option value="Entrada">Entrada</option>
                    <option value="Salida">Salida</option>
                </select>
            </div>
        </div>
        <div class="row mb-2">
            <div class="col-md-3" id="divMontoEntrada"><label>Monto</label><input type="number" class="form-control" step="0.01" name="monto" id="editMonto"></div>
            <div class="col-md-3" id="divLaborEntrada"><label>Labor</label>
                <select name="labor" class="form-control" id="editLaborEntrada">
                    <?php foreach($labores as $l): ?>
                        <option value="<?= $l['id_labor'] ?>"><?= htmlspecialchars($l['nombre_labor']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3" id="divFamiliaEntrada"><label>Familia</label>
                <select name="familia" class="form-control" id="editFamiliaEntrada">
                    <?php foreach($familias as $f): ?>
                        <option value="<?= $f['id_Familia'] ?>"><?= htmlspecialchars($f['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <!-- Productos salida -->
        <div id="divProductosSalida">
            <h6>Productos (Salida)</h6>
            <table class="table table-bordered" id="tablaProductosEdit">
                <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Unidad</th>
                        <th>Precio Unitario</th>
                        <th>Familia</th>
                        <th>Total</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
            <button type="button" class="btn btn-secondary" id="btnAgregarProductoEdit">Agregar Producto</button>
        </div>
        <div class="mb-2 mt-2">
            <label>Observación</label>
            <input type="text" class="form-control" name="observacion" id="editObservacion">
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Guardar Cambios</button>
        <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">Cancelar</button>
    </div>
</div>
</form>
</div>
</div>

<script>
// Feather icons
feather.replace();

// Toggle saldo ojo
const toggleSaldo = document.getElementById('toggleSaldo');
const valorSaldo = document.getElementById('valorSaldo');
toggleSaldo.addEventListener('click', ()=>{valorSaldo.style.opacity = valorSaldo.style.opacity==='0'?'1':'0';});

// Edit modal funcionalidad
const divMontoEntrada = document.getElementById('divMontoEntrada');
const divLaborEntrada = document.getElementById('divLaborEntrada');
const divFamiliaEntrada = document.getElementById('divFamiliaEntrada');
const divProductosSalida = document.getElementById('divProductosSalida');
const editTipo = document.getElementById('editTipo');

editTipo.addEventListener('change',()=>{
    if(editTipo.value==='Entrada'){
        divMontoEntrada.style.display='block';
        divLaborEntrada.style.display='block';
        divFamiliaEntrada.style.display='block';
        divProductosSalida.style.display='none';
    }else{
        divMontoEntrada.style.display='none';
        divLaborEntrada.style.display='block';
        divFamiliaEntrada.style.display='none';
        divProductosSalida.style.display='block';
    }
});

// Abrir modal editar
document.querySelectorAll('.btnEditar').forEach(btn=>{
    btn.addEventListener('click',()=>{
        const tr = btn.closest('tr');
        const id = tr.dataset.id;
        fetch('get_mov.php?id='+id)
        .then(res=>res.json())
        .then(data=>{
            document.getElementById('editIdMov').value = id;
            document.getElementById('editFecha').value = data.fecha;
            document.getElementById('editProveedor').value = data.proveedor;
            document.getElementById('editFactura').value = data.factura;
            document.getElementById('editTipo').value = data.tipo_entrada_salida;
            document.getElementById('editObservacion').value = data.observacion;
            document.getElementById('editLaborEntrada').value = data.labor;
            document.getElementById('editFamiliaEntrada').value = data.familia;

            // Productos salida
            const tbody = document.querySelector('#tablaProductosEdit tbody');
            tbody.innerHTML = '';
            if(data.tipo_entrada_salida==='Salida'){
                data.productos.forEach(p=>{
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td><input type="text" name="producto[]" class="form-control" value="${p.producto}"></td>
                        <td><input type="number" name="cantidad[]" class="form-control" value="${p.cantidad}"></td>
                        <td><input type="text" name="unidad[]" class="form-control" value="${p.unidad}"></td>
                        <td><input type="number" name="precio[]" class="form-control" value="${p.precio_unitario}"></td>
                        <td>
                            <select name="familia[]" class="form-control">
                                <?php foreach($familias as $f): ?>
                                <option value="<?= $f['id_Familia'] ?>"><?= htmlspecialchars($f['nombre']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                        <td>${p.Total}</td>
                        <td><button type="button" class="btn btn-danger btnEliminarFila">Eliminar</button></td>
                    `;
                    tbody.appendChild(row);
                    row.querySelector('.btnEliminarFila').addEventListener('click', ()=>{row.remove();});
                    row.querySelector('select[name="familia[]"]').value = p.id_Familia_Familia;
                });
            }

            new bootstrap.Modal(document.getElementById('editarModal')).show();
            editTipo.dispatchEvent(new Event('change'));
        });
    });
});

// Guardar cambios AJAX
document.getElementById('formEditar').addEventListener('submit', e=>{
    e.preventDefault();
    const formData = new FormData(e.target);
    fetch('update_mov.php',{method:'POST',body:formData})
    .then(res=>res.json())
    .then(data=>{
        if(data.success) location.reload();
        else alert(data.error);
    });
});

// Eliminar
document.querySelectorAll('.btnEliminar').forEach(btn=>{
    btn.addEventListener('click',()=>{
        if(confirm("¿Eliminar este movimiento?")){
            const tr = btn.closest('tr');
            const id = tr.dataset.id;
            const fd = new FormData();
            fd.append('id_mov',id);
            fetch('delete_mov.php',{method:'POST',body:fd})
            .then(res=>res.json())
            .then(data=>{
                if(data.success){
                    tr.remove();
                    fetch('get_saldo.php').then(r=>r.text()).then(s=>{
                        valorSaldo.textContent = s;
                    });
                } else alert(data.error);
            });
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>
