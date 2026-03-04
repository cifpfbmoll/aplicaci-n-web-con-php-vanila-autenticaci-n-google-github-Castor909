# Guía de Configuración OAuth - Instrucciones en Español

Esta es una guía complementaria en español para la configuración de credenciales OAuth 2.0.

## 📋 Índice

- [Configuración de Google OAuth](#configuración-de-google-oauth)
- [Configuración de GitHub OAuth](#configuración-de-github-oauth)
- [Configuración del Proyecto](#configuración-del-proyecto)
- [Ejecución del Proyecto](#ejecución-del-proyecto)

## Configuración de Google OAuth

### Paso 1: Acceder a Google Cloud Console

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Inicia sesión con tu cuenta de Google

### Paso 2: Crear un Proyecto

1. Haz clic en el menú desplegable de proyectos (arriba)
2. Selecciona "Nuevo Proyecto"
3. Nombre del proyecto: "OAuth PHP Authentication" (o el que prefieras)
4. Haz clic en "Crear"

### Paso 3: Habilitar Google+ API

1. En el menú lateral, ve a "APIs y servicios" → "Biblioteca"
2. Busca "Google+ API"
3. Haz clic en "Habilitar"

### Paso 4: Crear Credenciales OAuth

1. Ve a "APIs y servicios" → "Credenciales"
2. Haz clic en "Crear credenciales" → "ID de cliente de OAuth 2.0"
3. Si es primera vez, configura la pantalla de consentimiento:
   - Tipo de usuario: Externo
   - Nombre de la aplicación: "OAuth PHP App"
   - Correo electrónico de asistencia: tu correo
   - Haz clic en "Guardar y continuar"

### Paso 5: Configurar ID de Cliente

1. Tipo de aplicación: **Aplicación web**
2. Nombre: "PHP OAuth Client"
3. URIs de redireccionamiento autorizados:
   ```
   http://localhost:8000/oauth/google_callback.php
   ```
4. Haz clic en "Crear"

### Paso 6: Copiar Credenciales

Te aparecerá una ventana con:
- **ID de cliente**: Copia este valor
- **Secreto del cliente**: Copia este valor

**Guárdalos en un lugar seguro** - los necesitarás para el archivo `.env`

---

## Configuración de GitHub OAuth

### Paso 1: Acceder a Configuración de Desarrollador

1. Ve a [GitHub Developer Settings](https://github.com/settings/developers)
2. Inicia sesión en tu cuenta de GitHub

### Paso 2: Crear Nueva OAuth App

1. Haz clic en "OAuth Apps" en el menú lateral
2. Haz clic en "New OAuth App"

### Paso 3: Completar Formulario

Llena los siguientes campos:

- **Application name**: `OAuth PHP Authentication`
- **Homepage URL**: `http://localhost:8000`
- **Application description**: `PHP OAuth 2.0 authentication demo`
- **Authorization callback URL**: `http://localhost:8000/oauth/github_callback.php`

### Paso 4: Registrar Aplicación

1. Haz clic en "Register application"
2. Te mostrará la página de tu aplicación

### Paso 5: Generar Client Secret

1. En la página de tu aplicación, haz clic en "Generate a new client secret"
2. Confirma tu contraseña si se te solicita
3. **Copia el Client Secret INMEDIATAMENTE** (solo se muestra una vez)

### Paso 6: Copiar Client ID

En la misma página encontrarás:
- **Client ID**: Copia este valor
- **Client Secret**: El que acabas de generar

**Guárdalos en un lugar seguro** - los necesitarás para el archivo `.env`

---

## Configuración del Proyecto

### Paso 1: Instalar Dependencias

```bash
composer install
```

Si no tienes Composer instalado, descárgalo de: https://getcomposer.org/

### Paso 2: Crear Archivo de Configuración

```bash
cp .env.example .env
```

### Paso 3: Editar Archivo .env

Abre el archivo `.env` con tu editor favorito:

```bash
nano .env
# o
code .env
# o
vim .env
```

### Paso 4: Configurar Credenciales

Reemplaza los valores en `.env` con tus credenciales:

```env
# Google OAuth Settings
GOOGLE_CLIENT_ID=tu_client_id_de_google_aqui
GOOGLE_CLIENT_SECRET=tu_client_secret_de_google_aqui
GOOGLE_REDIRECT_URI=http://localhost:8000/oauth/google_callback.php

# GitHub OAuth Settings
GITHUB_CLIENT_ID=tu_client_id_de_github_aqui
GITHUB_CLIENT_SECRET=tu_client_secret_de_github_aqui
GITHUB_REDIRECT_URI=http://localhost:8000/oauth/github_callback.php

# Application Settings
APP_URL=http://localhost:8000
SESSION_SECURE=false
SESSION_HTTPONLY=true
```

**IMPORTANTE**: 
- No incluyas espacios alrededor del signo `=`
- No uses comillas a menos que sean parte de la credencial
- Guarda el archivo

### Paso 5: Inicializar Base de Datos

```bash
php setup_database.php
```

Deberías ver:
```
✓ Database and users table created successfully.
✓ Table supports both traditional and OAuth authentication.
✓ Ready to use!
```

---

## Ejecución del Proyecto

### Iniciar Servidor de Desarrollo

```bash
php -S localhost:8000
```

Deberías ver:
```
[Date Time] PHP 8.x.x Development Server (http://localhost:8000) started
```

### Acceder a la Aplicación

1. Abre tu navegador
2. Ve a: **http://localhost:8000**
3. Deberías ver la página de inicio de sesión

### Probar la Aplicación

#### Opción 1: Login con Google
1. Haz clic en "Continue with Google"
2. Selecciona tu cuenta de Google
3. Autoriza la aplicación
4. Serás redirigido al dashboard

#### Opción 2: Login con GitHub
1. Haz clic en "Continue with GitHub"
2. Autoriza la aplicación
3. Serás redirigido al dashboard

#### Opción 3: Registro Traditional
1. Haz clic en "Don't have an account? Register here"
2. Completa el formulario
3. Inicia sesión con tus credenciales

---

## Solución de Problemas Comunes

### Error: "Configuration Required"

**Causa**: Las credenciales OAuth no están configuradas correctamente en `.env`

**Solución**: 
1. Verifica que copiaste correctamente los valores de Google y GitHub
2. Asegúrate de no tener espacios extra
3. Reinicia el servidor PHP

### Error: "Redirect URI mismatch"

**Causa**: Las URIs configuradas en Google/GitHub no coinciden exactamente

**Solución**:
- **Google**: `http://localhost:8000/oauth/google_callback.php` (exacto)
- **GitHub**: `http://localhost:8000/oauth/github_callback.php` (exacto)

### Error: "Database connection error"

**Causa**: La base de datos no se creó correctamente

**Solución**:
```bash
rm database/usuarios.db
php setup_database.php
```

### Error: "Class 'Google_Client' not found"

**Causa**: Dependencias de Composer no instaladas

**Solución**:
```bash
composer install
```

### El servidor no inicia en el puerto 8000

**Causa**: Puerto ocupado

**Solución**: Usa otro puerto
```bash
php -S localhost:3000
# Actualiza .env con APP_URL=http://localhost:3000
# Actualiza las URIs de redirección en Google y GitHub
```

---

## Captura de Pantallas

Para la entrega del proyecto, necesitas incluir capturas de pantalla en la carpeta `pantallazos/`:

### Capturas Recomendadas:

1. **login.png**: Página de inicio de sesión
2. **oauth-buttons.png**: Botones de Google y GitHub
3. **google-consent.png**: Pantalla de consentimiento de Google
4. **github-authorize.png**: Pantalla de autorización de GitHub
5. **dashboard.png**: Dashboard con información del usuario
6. **profile-info.png**: Información del perfil
7. **traditional-login.png**: Login tradicional
8. **register.png**: Formulario de registro

### Cómo Hacer Capturas:

**Linux**: 
- Gnome Screenshot: `gnome-screenshot`
- Flameshot: `flameshot gui`

**Windows**: 
- `Windows + Shift + S`

**Mac**: 
- `Cmd + Shift + 4`

Guarda las imágenes en: `pantallazos/nombre-descriptivo.png`

---

## Verificación Final

Antes de entregar, verifica:

- [ ] `.env` configurado con credenciales válidas
- [ ] Dependencias instaladas (`vendor/` existe)
- [ ] Base de datos creada (`database/usuarios.db` existe)
- [ ] Login con Google funciona
- [ ] Login con GitHub funciona
- [ ] Registro tradicional funciona
- [ ] Dashboard muestra información correcta
- [ ] Logout funciona
- [ ] Capturas de pantalla incluidas
- [ ] README.md completo
- [ ] DOCUMENTATION.md revisado

---

## Recursos Adicionales

- [Documentación Completa (Inglés)](DOCUMENTATION.md)
- [Google OAuth Documentation](https://developers.google.com/identity/protocols/oauth2)
- [GitHub OAuth Documentation](https://docs.github.com/en/developers/apps/building-oauth-apps)
- [PHP Manual](https://www.php.net/manual/es/)

---

**¡Buena suerte con tu proyecto! 🚀**
