<?php
session_start();
require_once '../mvcmodelos/UsuarioModel.php';

$db = new PDO('mysql:host=localhost:3306;dbname=fixyourauto', 'root', '');
$usuarioModel = new UsuarioModel($db);

$error = '';
$success = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';
    $password_confirm = isset($_POST['password_confirm']) ? trim($_POST['password_confirm']) : '';

    // Validation
    if (empty($nombre) || empty($email) || empty($password) || empty($password_confirm)) {
        $error = 'Error: Todos los campos son obligatorios.';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Error: Formato de email no válido.';

    } elseif ($password !== $password_confirm) {
        $error = 'Error: Las contraseñas no coinciden.';

    } elseif (strlen($password) < 6) {
        $error = 'Error: La contraseña debe tener al menos 6 caracteres.';

    } elseif ($usuarioModel->getUsuarioByEmail($email)) {
        $error = 'Error: Este email ya está registrado.';

    } else {
        if ($usuarioModel->registrarUsuario($nombre, $email, $password)) {
            $success = 'Registro exitoso. Ahora puedes iniciar sesión.';
            header("refresh:3;url=login.php");
        } else {
            $error = 'Error al registrar el usuario. Inténtalo de nuevo.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - FixYourAuto</title>
    <link rel="stylesheet" href="../css/register.css">
</head>
<body>
<div class="register-container">
    <div class="register-header">
        <img src="../img/logo.png" alt="Logo FixYourAuto">
        <h2>Crear Cuenta Profesional</h2>
        <p>Únete a FixYourAuto y accede a precios exclusivos para talleres.</p>
    </div>

    <?php if(!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if(!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="nombre">Nombre Completo:</label>
            <input type="text" id="nombre" name="nombre" required>
        </div>

        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <div class="form-group">
            <label for="password_confirm">Confirmar Contraseña:</label>
            <input type="password" id="password_confirm" name="password_confirm" required>
        </div>

        <button type="submit" class="btn-register">Registrarse</button>

        <div class="login-option">
            <p>¿Ya tienes una cuenta? <a href="login.php">Iniciar Sesión</a></p>
            <p><a href="../">Volver al Inicio</a></p>
        </div>
    </form>
</div>
</body>
</html>