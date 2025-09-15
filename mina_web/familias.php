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

    $stmt = $pdo->prepare("UPDATE familia SET nombre = ? WHERE id_Familia = ?");
    $stmt->execute([$nombre, $id]);
    $mensaje = "Familia actualizada correctamente.";
}

// Procesar eliminación
if(isset($_GET['eliminar'])){
    $id = intval($_GET['eliminar']);
    // Aquí podrías chequear relaciones si es necesario
    $stmtDel = $pdo->prepare("DELETE FROM familia WHERE id_Familia = ?");
    $stmtDel->execute([$id]);
    $mensaje = "Familia eliminada correctamente.";
}

// Agregar nueva familia
if(isset($_POST['agregar'])){
    $nombreNuevo = $_POST['nuevo_nombre'];

    $stmt = $pdo->prepare("INSERT INTO familia (nombre) VALUES (?)");
    $stmt->execute([$nombreNuevo]);
    $mensaje = "Familia agregada correctamente.";
}

// Obtener lista de familias
$stmt = $pdo->query("SELECT id_Familia, nombre FROM familia");
$familias = $stmt->fetchAll();
?>

<h1 class="h2">Listado de Familias</h1>

<?php if(isset($mensaje)): ?>
    <div class="alert alert-info"><?= htmlspecialchars($mensaje) ?></div>
<?php endif; ?>

<table class="table table-striped table-bordered">
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($familias as $fam): ?>
        <tr data-id="<?= $fam['id_Familia'] ?>">
            <td class="nombre"><?= htmlspecialchars($fam['nombre']) ?></td>
            <td>
                <button class="btn btn-sm btn-warning editarBtn">Editar</button>
                <a href="familias.php?eliminar=<?= $fam['id_Familia'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Seguro que deseas eliminar esta familia?')">Eliminar</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Botón Agregar Nueva Familia -->
<button id="btnAgregar" class="btn btn-primary mb-3">Agregar Nueva Familia</button>

<!-- Formulario oculto para nueva familia -->
<div id="formAgregar" style="display:none; max-width:400px;">
    <form method="post" class="card p-3 mb-3">
        <div class="mb-3">
            <label>Nombre</label>
            <input type="text" name="nuevo_nombre" class="form-control" required>
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

        const nombre = nombreCell.textContent;

        nombreCell.innerHTML = `<input type="text" class="form-control" value="${nombre}">`;

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

            const form = document.createElement('form');
            form.method = 'post';
            form.innerHTML = `
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="nombre" value="${nuevoNombre}">
                <input type="hidden" name="guardar" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        });

        cancelarBtn.addEventListener('click', function() {
            nombreCell.textContent = nombre;
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
