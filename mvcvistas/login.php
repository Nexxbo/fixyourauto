<?php
session_start();
require_once '../mvcmodelos/UsuarioModel.php';

$db = new PDO('mysql:host=localhost:3306;dbname=fixyourauto', 'root', '');
$usuarioModel = new UsuarioModel($db);

$error = '';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    $usuario = $usuarioModel->getUsuarioByEmail($email);

    // Verificación de contraseña en texto plano
    if($usuario && $password === $usuario['password']) {
        $_SESSION['usuario'] = array(
            'id' => $usuario['id'],
            'nombre' => $usuario['nombre'],
            'email' => $usuario['email']
        );
        header('Location: search.php');
        exit;
    } else {
        $error = 'Error: Credenciales incorrectas. Por favor, verifica tu email y contraseña.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - FixYourAuto</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <img  src="../img/logo.png" alt="Logo">
        <h2>Acceso Profesionales</h2>
    </div>

    <?php if(!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
        </div>

        <button type="submit" class="btn-login">Acceder</button>

        <div class="login-option">
            <p>¿No tienes cuenta? <a href="register.php">Regístrate aquí</a></p> <p><a href="../">Salir</a></p>
        </div>
    </form>
</div>
</body>
</html>