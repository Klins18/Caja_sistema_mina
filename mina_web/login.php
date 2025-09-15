<?php
session_start();
include 'includes/db.php'; // $pdo disponible

if(isset($_POST['login'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    try {
        // Consulta segura con prepared statement
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = :usuario");
        $stmt->execute(['usuario' => $usuario]);
        $row = $stmt->fetch();

        if($row) {
            if(password_verify($password, $row['password'])){
                $_SESSION['usuario'] = $row['usuario'];
                $_SESSION['rol'] = $row['rol'];
                header("Location: index.php");
                exit();
            } else {
                $error = "Contraseña incorrecta";
            }
        } else {
            $error = "Usuario no encontrado";
        }
    } catch (PDOException $e) {
        $error = "Error en la base de datos: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Gestión de Mina Web</title>
    <!-- Bootstrap CSS offline -->
    <link href="assets/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <!-- Estilos personalizados -->
    <link href="assets/css/styles.css" rel="stylesheet">
</head>
<body class="bg-light d-flex justify-content-center align-items-center vh-100">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-sm-8 col-md-6 col-lg-4">
            <div class="card p-4 shadow login-card">
                <h3 class="mb-3 text-center">Iniciar Sesión</h3>

                <?php if(isset($error)): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <?= htmlspecialchars($error) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label>Usuario</label>
                        <input type="text" name="usuario" class="form-control" placeholder="Ingrese usuario" required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" placeholder="Ingrese contraseña" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100 btn-login">Ingresar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS offline -->
<script src="assets/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>
</html>
