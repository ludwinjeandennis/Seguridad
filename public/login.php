<?php
// public/login.php
session_start();
require_once '../includes/db.php';
require_once '../includes/funciones.php';

$mensaje = '';
$tipo_mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $correo = sanitizarEntrada($_POST['correo']);
    $password = $_POST['password'];

    $stmt = $conexion->prepare("SELECT id, nombre, contrasena_hash FROM usuarios WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $nombre, $hash);
        $stmt->fetch();
        if (verificarPassword($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['usuario_id'] = $id;
            $_SESSION['nombre'] = $nombre;
            header("Location: dashboard.php");
            exit;
        } else {
            $mensaje = "Contraseña incorrecta. Por favor, verifica tus credenciales.";
            $tipo_mensaje = "error";
        }
    } else {
        $mensaje = "No se encontró una cuenta con ese correo electrónico.";
        $tipo_mensaje = "error";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <meta name="description" content="Inicia sesión en tu cuenta">
    <style>
        /* Variables CSS (CSS Custom Properties) */
:root {
    --primary-color: #2563eb;
    --primary-hover: #1d4ed8;
    --secondary-color: #64748b;
    --success-color: #059669;
    --warning-color: #d97706;
    --error-color: #dc2626;
    --background: #f8fafc;
    --card-bg: #ffffff;
    --border-color: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    
    /* Sombras y gradientes */
    --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
    --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
    --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Reset y configuración base */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', 'Lato', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
    background: var(--background);
    color: var(--text-primary);
    line-height: 1.6;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1rem;
}

/* Container principal */
.login-container {
    width: 100%;
    max-width: 420px;
    position: relative;
}

/* Header con gradiente */
.login-header {
    text-align: center;
    margin-bottom: 2rem;
    position: relative;
    padding: 2rem 0;
}

.login-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: var(--gradient-primary);
    border-radius: 2px;
}

.login-header h2 {
    font-size: 2rem;
    font-weight: 700;
    background: var(--gradient-primary);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 0.5rem;
}

.login-header p {
    color: var(--text-secondary);
    font-weight: 400;
}

/* Card principal */
.login-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 2.5rem;
    box-shadow: var(--shadow-lg);
    border: 1px solid var(--border-color);
    position: relative;
    overflow: hidden;
}

.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--gradient-primary);
}

/* Formulario */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

/* Grupos de campos */
.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 500;
    color: var(--text-primary);
    font-size: 0.875rem;
}

/* Inputs modernos */
.form-input {
    padding: 0.875rem 1rem;
    border: 2px solid var(--border-color);
    border-radius: 8px;
    font-size: 1rem;
    transition: all 0.2s ease;
    background: var(--card-bg);
    color: var(--text-primary);
}

.form-input:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgb(37 99 235 / 0.1);
}

.form-input::placeholder {
    color: var(--text-secondary);
}

/* Botón principal */
.btn-primary {
    background: var(--gradient-primary);
    color: white;
    border: none;
    border-radius: 8px;
    padding: 0.875rem 1.5rem;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
    text-decoration: none;
    display: inline-block;
    text-align: center;
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s ease;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:active {
    transform: translateY(0);
}

/* Enlaces */
.link-secondary {
    color: var(--primary-color);
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s ease;
    text-align: center;
    display: block;
    margin-top: 1rem;
}

.link-secondary:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}

/* Mensajes de estado */
.message {
    padding: 0.875rem 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
    font-weight: 500;
    animation: slideIn 0.3s ease-out;
    border-left: 4px solid;
}

.message.error {
    background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(220, 38, 38, 0.05));
    color: var(--error-color);
    border-left-color: var(--error-color);
}

.message.success {
    background: linear-gradient(135deg, rgba(5, 150, 105, 0.1), rgba(16, 185, 129, 0.05));
    color: var(--success-color);
    border-left-color: var(--success-color);
}

.message.warning {
    background: linear-gradient(135deg, rgba(217, 119, 6, 0.1), rgba(245, 158, 11, 0.05));
    color: var(--warning-color);
    border-left-color: var(--warning-color);
}

/* Animaciones */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Responsive */
@media (max-width: 768px) {
    .login-container {
        max-width: 100%;
        padding: 0 1rem;
    }
    
    .login-card {
        padding: 2rem;
    }
    
    .login-header h2 {
        font-size: 1.75rem;
    }
}

/* Efectos adicionales para interactividad */
.login-card {
    transition: transform 0.3s ease;
}

.login-card:hover {
    transform: translateY(-2px);
}

/* Decoración de fondo */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: 
        radial-gradient(circle at 20% 20%, rgba(37, 99, 235, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
    z-index: -1;
    pointer-events: none;
}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2>Bienvenido de nuevo</h2>
            <p>Inicia sesión en tu cuenta</p>
        </div>

        <div class="login-card">
            <?php if (!empty($mensaje)): ?>
                <div class="message <?php echo $tipo_mensaje; ?>">
                    <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <form method="post" class="login-form">
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input 
                        type="email" 
                        id="correo" 
                        name="correo" 
                        class="form-input" 
                        placeholder="ejemplo@correo.com"
                        value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                        required
                        autocomplete="email"
                    >
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Ingresa tu contraseña"
                        required
                        autocomplete="current-password"
                    >
                </div>

                <button type="submit" name="login" class="btn-primary">
                    Iniciar Sesión
                </button>

                <a href="registrar.php" class="link-secondary">
                    ¿No tienes cuenta? Regístrate aquí
                </a>
            </form>
        </div>
    </div>
</body>
</html>