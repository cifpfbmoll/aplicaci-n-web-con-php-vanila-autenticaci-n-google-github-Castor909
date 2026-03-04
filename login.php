<?php
include 'conexion.php';
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/oauth_config.php';
session_start();

// Настроить клиент Google
$client = new Google_Client();
$client->setClientId(GOOGLE_CLIENT_ID);
$client->setClientSecret(GOOGLE_CLIENT_SECRET);
$client->setRedirectUri(GOOGLE_REDIRECT_URI);
$client->addScope('email');
$client->addScope('profile');
$authUrl = $client->createAuthUrl();

// Обработка редиректа от Google (код авторизации)
if (isset($_GET['code'])) {
  try {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
  } catch (Exception $e) {
    echo 'Error al obtener token: ' . htmlspecialchars($e->getMessage());
    exit;
  }

  if (empty($token) || isset($token['error'])) {
    echo 'Error en el token: ' . htmlspecialchars(json_encode($token));
    exit;
  }

  $client->setAccessToken($token);
  $oauth2 = new Google_Service_Oauth2($client);
  $google_account_info = $oauth2->userinfo->get();
  $email = $google_account_info->email ?? null;
  $name = $google_account_info->name ?? null;

  if (!$email) {
    echo 'No se obtuvo email desde Google.';
    exit;
  }

  // Связываем/создаём пользователя в локальной БД
  $db = conectar();
  $stmt = $db->prepare('SELECT usuario FROM usuarios WHERE usuario = ?');
  $stmt->execute([$email]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    // Уже существует — логин
    session_regenerate_id(true);
    $_SESSION['usuario'] = $row['usuario'];
    header('Location: protected.php');
    exit;
  }

  // Создаём нового пользователя: задаём случайную пароль-хэш, чтобы не ломать NOT NULL
  try {
    $random = bin2hex(random_bytes(16));
  } catch (Exception $e) {
    $random = uniqid('g_', true);
  }
  $hash = password_hash($random, PASSWORD_DEFAULT);

  try {
    $ins = $db->prepare('INSERT INTO usuarios (usuario, password) VALUES (?, ?)');
    $ins->execute([$email, $hash]);
    session_regenerate_id(true);
    $_SESSION['usuario'] = $email;
    header('Location: protected.php');
    exit;
  } catch (PDOException $e) {
    echo 'Error al crear usuario: ' . htmlspecialchars($e->getMessage());
    exit;
  }
}

// Обработка формы традиционного входа
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
  <hr>
  <p>O</p>
  <p>
    <a href="<?php echo htmlspecialchars($authUrl); ?>"><img src="https://developers.google.com/identity/images/btn_google_signin_light_normal_web.png" alt="Iniciar con Google"></a>
  </p>
  <p><a href="registro.php">Crear cuenta</a></p>
</body>
</html>