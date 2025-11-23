<?php
session_start();
if (empty($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Área protegida</title>
</head>
<body>
  <h1>Bienvenido, <?= htmlspecialchars($_SESSION['usuario']) ?></h1>
  <p>Esta es una página protegida.</p>
  <p><a href="logout.php">Cerrar sesión</a></p>
</body>
</html>