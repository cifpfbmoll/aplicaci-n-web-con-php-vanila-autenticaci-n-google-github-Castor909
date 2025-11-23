<?php
include 'conexion.php';

function responder($msg) {
    echo htmlspecialchars($msg);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $clave = $_POST['clave'] ?? '';

    // Validación: campos vacíos
    if ($usuario === '' || $clave === '') {
        responder('Error: rellene todos los campos.');
    }

    // Validación: longitud
    if (mb_strlen($usuario) < 3) {
        responder('Error: el nombre de usuario debe tener al menos 3 caracteres.');
    }
    if (mb_strlen($clave) < 6) {
        responder('Error: la contraseña debe tener al menos 6 caracteres.');
    }

    // Validación: caracteres permitidos para el nombre de usuario
    if (!preg_match('/^[A-Za-z0-9_-]+$/u', $usuario)) {
        responder('Error: el nombre de usuario solo puede contener letras, números, "_" y "-".');
    }

    $hash = password_hash($clave, PASSWORD_DEFAULT);
    $db = conectar();

    try {
        $stmt = $db->prepare('INSERT INTO usuarios (usuario, password) VALUES (?, ?)');
        $stmt->execute([$usuario, $hash]);
        responder('Usuario registrado correctamente.');
    } catch (PDOException $e) {
        // Manejo de unicidad: SQLite suele devolver código 23000 o mensaje con UNIQUE
        if ($e->getCode() === '23000' || stripos($e->getMessage(), 'unique') !== false) {
            responder('Error: el usuario ya existe.');
        }
        responder('Error al registrar. Intente más tarde.');
    }
}
?>
<!-- Formulario de registro en español -->
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Registro</title>
</head>
<body>
  <h1>Registro</h1>
  <form method="POST" action="">
    <label>Usuario:<br><input type="text" name="usuario" required></label><br><br>
    <label>Contraseña:<br><input type="password" name="clave" required></label><br><br>
    <button type="submit">Registrar</button>
  </form>
</body>
</html>