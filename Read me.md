A continuación se presenta un **proyecto PHP funcional con SQLite** que implementa registro y login usando `password_hash()` y `password_verify()`, junto con documentación en formato **Markdown**.  

***

# Proyecto: Sistema de Autenticación con SQLite y Hash Seguro

## Resumen
Este proyecto demuestra cómo crear usuarios y validar contraseñas usando `password_hash()` y `password_verify()` con **SQLite** y **PDO**.[1][2][3]
El objetivo es almacenar contraseñas de manera segura y autenticarlas sin errores de comparación directa.

***

## Estructura del proyecto
```
/proyecto-login/
│
├─ database/
│  └─ usuarios.db
│
├─ conexion.php
├─ registro.php
├─ login.php
├─ crear_tabla.php
└─ README.md
```

***

## 1. Base de datos: `crear_tabla.php`
Este script crea la base SQLite y la tabla `usuarios`.

```php
<?php
try {
    $db = new PDO("sqlite:database/usuarios.db");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $db->exec("CREATE TABLE IF NOT EXISTS usuarios (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        usuario TEXT UNIQUE,
        password TEXT NOT NULL
    )");

    echo "Base de datos y tabla creadas correctamente.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
```

***

## 2. Conexión: `conexion.php`
Conecta con la base SQLite mediante **PDO**.

```php
<?php
function conectar() {
    try {
        $db = new PDO("sqlite:database/usuarios.db");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}
?>
```

***

## 3. Registro de usuario: `registro.php`

```php
<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];
    $hash = password_hash($clave, PASSWORD_DEFAULT);

    $db = conectar();
    $stmt = $db->prepare("INSERT INTO usuarios (usuario, password) VALUES (?, ?)");
    if ($stmt->execute([$usuario, $hash])) {
        echo "Usuario registrado correctamente.";
    } else {
        echo "Error al registrar.";
    }
}
?>

<form method="POST">
  Usuario: <input type="text" name="usuario" required><br>
  Contraseña: <input type="password" name="clave" required><br>
  <button type="submit">Registrar</button>
</form>
```

***

## 4. Inicio de sesión: `login.php`

```php
<?php
include('conexion.php');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    $db = conectar();
    $stmt = $db->prepare("SELECT password FROM usuarios WHERE usuario = ?");
    $stmt->execute([$usuario]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && password_verify($clave, $row["password"])) {
        echo "Inicio de sesión correcto.";
    } else {
        echo "Usuario o contraseña incorrectos.";
    }
}
?>

<form method="POST">
  Usuario: <input type="text" name="usuario" required><br>
  Contraseña: <input type="password" name="clave" required><br>
  <button type="submit">Ingresar</button>
</form>
```

***

## 5. Documentación técnica (README.md)

### Esquema de trabajo
- **Registro:**  
  - La contraseña se procesa con `password_hash()`.  
  - El hash resultante se guarda directamente en la base de datos SQLite.

- **Login:**  
  - Se obtiene el hash del usuario desde la base.  
  - `password_verify()` compara internamente la contraseña ingresada con el hash almacenado.

### Aspectos clave de seguridad
- `password_hash()` crea un *salt* aleatorio y lo incluye en el hash.
- `password_verify()` extrae ese *salt* y verifica sin necesidad de almacenar datos adicionales.[4][5]
- Los hashes cambian cada vez, incluso con contraseñas iguales, garantizando unicidad y resistencia ante ataques.

### Dependencias y configuración
- PHP 8.0 o superior con extensión `pdo_sqlite` habilitada.
- Carpeta `database/` debe existir y ser escribible (CHMOD 775).

### Ejecución
1. Crea la base con `crear_tabla.php` (ejecutar una sola vez).  
2. Registra usuarios en `registro.php`.  
3. Inicia sesión desde `login.php`.

### Resultados esperados
- Cada usuario se almacena con una contraseña encriptada.  
- El login valida correctamente sin errores de comparación.  
- La base `usuarios.db` contendrá entradas en texto no legible.

***

### Log del desarrollo
| Fecha | Acción | Detalle técnico |
|-------|---------|----------------|
| 2025-10-22 | Diseño del esquema | Se eligió SQLite por portabilidad y simplicidad [1][6]. |
| 2025-10-22 | Implementación inicial | Estructura PDO modular. |
| 2025-10-22 | Seguridad añadida | Uso de `password_hash()` + `password_verify()`, sin almacenamiento del salt. |
| 2025-10-22 | Pruebas funcionales | Registro y login verificados localmente con hash bcrypt. |

***

Este proyecto puede ejecutarse en cualquier entorno local (XAMPP, Laragon, VSCode + PHP Server) y es completamente autocontenido, sin necesidad de servidor MySQL.

Fuentes
[1] PHP Data Objects (PDO) https://www.mclibre.org/consultar/php/lecciones/php-db-pdo.html
[2] Autenticación Segura: Guía Práctica de PHP y PDO https://nelkodev.com/php/autenticacion-robusta-con-php-y-pdo-tu-guia-practica/
[3] Autenticar usuario usando PDO y password_verify() https://www.baulphp.com/autenticar-usuario-usando-pdo-y-password_verify/
[4] password_hash - Manual https://www.php.net/manual/es/function.password-hash.php
[5] Implementación de password_verify() y password_hash ... https://es.stackoverflow.com/questions/605265/implementaci%C3%B3n-de-password-verify-y-password-hash-para-la-validaci%C3%B3n-de-usua
[6] SQLite en PHP https://guiaphp.com/bbdd/sqlite-en-php/
[7] Como crear usuarios en PHP con password hasheada https://discoduroderoer.es/como-crear-usuarios-en-php-con-password-hasheada/
[8] Formulario de inicio de sesión con PHP y MySQL https://www.cursosdesarrolloweb.es/blog/formulario-de-inicio-de-sesion-con-php-y-my-sql
[9] Crear Usuarios y Contraseña con PHP y SQLite https://www.youtube.com/watch?v=cokFwMu5mp4
[10] Lección 18: Registro | Curso PHP 8.x https://programadorwebvalencia.com/cursos/php/registro/
[11] SQLite3 y PDO con PHP: crud y ejemplos https://parzibyte.me/blog/posts/php-sqlite3-pdo-crud-ejemplos/
[12] php - ¿Cómo Utilizar password_verify()? https://es.stackoverflow.com/questions/460888/c%C3%B3mo-utilizar-password-verify
[13] LOGIN Y REGISTRO CON CONTRASEÑA ENCRIPTADA ... https://vaidrollteam.blogspot.com/2021/03/login-con-contrasena-encriptada-en-php.html
[14] Base de Datos #11 - Login con password_verify https://www.youtube.com/watch?v=O5pPMlESM1I
[15] GitHub - jvadillo/guia-php-pdo: Guía de PDO https://github.com/jvadillo/guia-php-pdo
[16] password_verify - Manual https://www.php.net/manual/es/function.password-verify.php
[17] Lección 17: Login | Curso PHP 8.x https://programadorwebvalencia.com/cursos/php/login/
[18] Curso de PHP🐘 y MySql🐬 [69.- Conexión a MySQL y SQLite vía PDO] https://www.youtube.com/watch?v=MpRPGYkLeSU
[19] Cifrando passwords con php https://dev.to/nahuelsegovia/cifrando-passwords-con-php-4on1
[20] Login PHP con password_hash() http://pabletoreto.blogspot.com/2015/04/login-php-con-hash-de-password.html
