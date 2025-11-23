<?php
include 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    if ($usuario === '' || $clave === '') {
        echo 'Error: rellene todos los campos.';
        exit;
    }

    $db = conectar();
    $stmt = $db->prepare('SELECT password FROM usuarios WHERE usuario = ?');
    $stmt->execute([$usuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($clave, $row['password'])) {
        session_regenerate_id(true);
        $_SESSION['usuario'] = $usuario;
        header('Location: protected.php');
        exit;
    } else {
        echo 'Usuario o contraseña incorrectos.';
        exit;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Iniciar sesión</title>
</head>
<body>
  <h1>Iniciar sesión</h1>
  <form method="POST" action="">
    <label>Usuario:<br><input type="text" name="usuario" required></label><br><br>
    <label>Contraseña:<br><input type="password" name="clave" required></label><br><br>
    <button type="submit">Entrar</button>
  </form>
  <p><a href="registro.php">Crear cuenta</a></p>
</body>
</html>