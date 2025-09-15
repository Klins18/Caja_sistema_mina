<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';
include 'includes/header.php';

// Procesar actualización
if(isset($_POST['guardar'])){
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];

    $stmt = $pdo->prepare("UPDATE labor SET nombre_labor = ?, descripcion = ? WHERE id_labor = ?");
    $stmt->execute([$nombre, $descripcion, $id]);
    $mensaje = "Labor actualizada correctamente.";
}

// Procesar eliminación
if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    // Aquí podrías chequear relaciones si es necesario
    $stmtDel = $pdo->prepare("DELETE FROM labor WHERE id_labor = ?");
    $stmtDel->execute([$id]);
    $mensaje = "Labor eliminada correctamente.";
}

// Agregar nuevo labor
if(isset($_POST['agregar'])){
    $nombreNuevo = $_POST['nuevo_nombre'];
    $descripcionNueva = $_POST['nueva_descripcion'];

    $stmt = $pdo->prepare("INSERT INTO labor (nombre_labor, descripcion) VALUES (?, ?)");
    $stmt->execute([$nombreNuevo, $descripcionNueva]);
    $mensaje = "Labor agregada correctamente.";
}

// Obtener lista de labores
$stmt = $pdo->query("SELECT id_labor, nombre_labor, descripcion FROM labor");
$labores = $stmt->fetchAll();
?>

<h1 class="h2">Listado de Labores</h1>

<?php if(isset($mensaje)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($labores as $lab): ?>
        <tr data-id="<?= $lab['id_labor'] ?>">
            <td class="nombre"><?= htmlspecialchars($lab['nombre_labor']) ?></td>
            <td class="descripcion"><?= htmlspecialchars($lab['descripcion']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning editarBtn">Editar</button>
                <a href="labores.php?eliminar=<?= $lab['id_labor'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta labor?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Botón Agregar Nuevo Labor -->
<button id="btnAgregar" class="btn btn-primary mb-3">Agregar Nueva Labor</button>

<!-- Formulario oculto para nuevo labor -->
<div id="formAgregar" style="display:none; max-width:400px;">
    <form method="post" class="card p-3 mb-3">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nuevo_nombre" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Descripción</label>
            <input type="text" name="nueva_descripcion" class="form-control" required>
        </div>
        <button type="submit" name="agregar" class="btn btn-success">Agregar</button>
        <button type="button" id="cancelAgregar" class="btn btn-secondary">Cancelar</button>
    </form>
</div>

<script>
document.querySelectorAll('.editarBtn').forEach(button => {
    button.addEventListener('click', function() {
        const row = this.closest('tr');
        const id = row.dataset.id;
        const nombreCell = row.querySelector('.nombre');
        const descripcionCell = row.querySelector('.descripcion');

        const nombre = nombreCell.textContent;
        const descripcion = descripcionCell.textContent;

        nombreCell.innerHTML = `<input type="text" class="form-control" value="${nombre}">`;
        descripcionCell.innerHTML = `<input type="text" class="form-control" value="${descripcion}">`;

        this.style.display = 'none';
        const guardarBtn = document.createElement('button');
        guardarBtn.textContent = 'Guardar';
        guardarBtn.className = 'btn btn-sm btn-success me-1';
        const cancelarBtn = document.createElement('button');
        cancelarBtn.textContent = 'Cancelar';
        cancelarBtn.className = 'btn btn-sm btn-secondary';

        row.querySelector('td:last-child').prepend(guardarBtn, cancelarBtn);

        guardarBtn.addEventListener('click', function() {
            const nuevoNombre = nombreCell.querySelector('input').value;
            const nuevaDescripcion = descripcionCell.querySelector('input').value;

            const form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = `
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="nombre" value="${nuevoNombre}">
                <input type="hidden" name="descripcion" value="${nuevaDescripcion}">
                <input type="hidden" name="guardar" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        });

        cancelarBtn.addEventListener('click', function() {
            nombreCell.textContent = nombre;
            descripcionCell.textContent = descripcion;
            guardarBtn.remove();
            cancelarBtn.remove();
            button.style.display = 'inline-block';
        });
    });
});

// Mostrar formulario de agregar
document.getElementById('btnAgregar').addEventListener('click', function(){
    document.getElementById('formAgregar').style.display = 'block';
    this.style.display = 'none';
});

// Cancelar agregar
document.getElementById('cancelAgregar').addEventListener('click', function(){
    document.getElementById('formAgregar').style.display = 'none';
    document.getElementById('btnAgregar').style.display = 'inline-block';
});
</script>

<?php include 'includes/footer.php'; ?>
