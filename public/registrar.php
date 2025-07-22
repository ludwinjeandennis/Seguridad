<?php
// public/registrar.php
require_once '../includes/db.php';
require_once '../includes/funciones.php';

$mensaje = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registro'])) {
    $nombre = sanitizarEntrada($_POST['nombre']);
    $correo = sanitizarEntrada($_POST['correo']);
    $password = $_POST['password'];
    $confirmar_password = $_POST['confirmar_password'];

    // Validaciones
    if (empty($nombre) || empty($correo) || empty($password)) {
        $error = "Todos los campos son obligatorios.";
    } elseif ($password !== $confirmar_password) {
        $error = "Las contraseñas no coinciden.";
    } elseif (!validarPassword($password)) {
        $error = "La contraseña debe tener al menos 8 caracteres.";
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = "El correo electrónico no es válido.";
    } else {
        // Verificar si el correo ya existe
        $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE correo = ?");
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();
        
        if ($stmt->num_rows > 0) {
            $error = "El correo electrónico ya está registrado.";
        } else {
            // Proceder con el registro
            $hash = hashConSal($password);
            $claves = generarClavesRSA();

            if ($claves !== false) {
                $stmt_insert = $conexion->prepare("INSERT INTO usuarios (nombre, correo, contrasena_hash, clave_privada, clave_publica) VALUES (?, ?, ?, ?, ?)");
                $stmt_insert->bind_param("sssss", $nombre, $correo, $hash, $claves['privada'], $claves['publica']);
                
                if ($stmt_insert->execute()) {
                    $mensaje = "Usuario registrado correctamente. Ya puede iniciar sesión.";
                } else {
                    $error = "Error al registrar usuario: " . $stmt_insert->error;
                }
                $stmt_insert->close();
            } else {
                $error = "Error al generar las claves de seguridad.";
            }
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario - Sistema RSA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
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
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --gradient-error: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .header {
            background: var(--gradient-primary);
            padding: 2rem 0;
            position: relative;
            overflow: hidden;
            text-align: center;
            color: white;
            margin-bottom: 2rem;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: repeating-linear-gradient(
                45deg,
                transparent,
                transparent 2px,
                rgba(255,255,255,0.03) 2px,
                rgba(255,255,255,0.03) 4px
            );
            animation: float 20s linear infinite;
        }

        @keyframes float {
            0% { transform: translateX(-50px) translateY(-50px); }
            100% { transform: translateX(50px) translateY(50px); }
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
            position: relative;
            z-index: 1;
        }

        .header h2 {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .alert {
            padding: 1rem 1.25rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideIn 0.3s ease-out;
        }

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

        .alert-error {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1) 0%, rgba(220, 38, 38, 0.1) 100%);
            border: 1px solid rgba(220, 38, 38, 0.2);
            color: var(--error-color);
        }

        .alert-success {
            background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
            border: 1px solid rgba(5, 150, 105, 0.2);
            color: var(--success-color);
        }

        .registro-card {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 2rem;
            position: relative;
        }

        .registro-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .card-content {
            padding: 2rem;
        }

        table {
            width: 100%;
            border-spacing: 0;
        }

        table tr {
            margin-bottom: 1.5rem;
            display: block;
        }

        table td {
            display: block;
            padding: 0;
        }

        table td:first-child {
            margin-bottom: 0.5rem;
        }

        label {
            font-weight: 500;
            color: var(--text-primary);
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.2s ease;
            background: var(--card-bg);
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        input[type="text"]:hover,
        input[type="email"]:hover,
        input[type="password"]:hover {
            border-color: var(--secondary-color);
        }

        small {
            font-size: 0.75rem;
            color: var(--text-secondary);
            margin-top: 0.25rem;
            display: block;
        }

        .button-row {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
        }

        input[type="submit"],
        input[type="reset"] {
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            position: relative;
            overflow: hidden;
            font-family: inherit;
            flex: 1;
        }

        input[type="submit"] {
            background: var(--gradient-primary);
            color: white;
        }

        input[type="reset"] {
            background: var(--card-bg);
            color: var(--text-secondary);
            border: 2px solid var(--border-color);
        }

        input[type="submit"]:hover,
        input[type="reset"]:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        input[type="submit"]::before,
        input[type="reset"]::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(
                90deg,
                transparent,
                rgba(255, 255, 255, 0.2),
                transparent
            );
            transition: left 0.5s;
        }

        input[type="submit"]:hover::before,
        input[type="reset"]:hover::before {
            left: 100%;
        }

        hr {
            border: none;
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border-color), transparent);
            margin: 2rem 0;
        }

        .info-section {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-md);
            padding: 1.5rem;
            margin-bottom: 2rem;
            position: relative;
        }

        .info-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-success);
            border-radius: 16px 16px 0 0;
        }

        .info-section h3 {
            color: var(--text-primary);
            margin-bottom: 1rem;
            font-size: 1.125rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-section ul {
            list-style: none;
            display: grid;
            gap: 0.75rem;
        }

        .info-section li {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .info-section li::before {
            content: '✓';
            background: var(--gradient-success);
            color: white;
            width: 1.25rem;
            height: 1.25rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            font-weight: bold;
            flex-shrink: 0;
        }

        nav {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 2rem;
        }

        nav a {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.25rem;
            background: var(--card-bg);
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 8px;
            border: 2px solid var(--border-color);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        nav a:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
            border-color: var(--primary-color);
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .card-content {
                padding: 1.5rem;
            }
            
            .button-row {
                flex-direction: column;
            }
            
            nav {
                flex-direction: column;
                align-items: center;
            }

            table tr:last-child td {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <h1>🔐 Sistema de Encriptación RSA</h1>
            <h2>Registrar Nuevo Usuario</h2>
        </div>
    </div>

    <div class="container">
        <?php if ($error): ?>
            <div class="alert alert-error">
                <span>⚠️</span>
                <div>
                    <strong>Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($mensaje): ?>
            <div class="alert alert-success">
                <span>✅</span>
                <div>
                    <strong>Éxito:</strong> <?php echo htmlspecialchars($mensaje); ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="registro-card">
            <div class="card-content">
                <form method="post">
                    <table>
                        <tr>
                            <td><label for="nombre">👤 Nombre completo:</label></td>
                            <td><input type="text" name="nombre" id="nombre" maxlength="100" required 
                                       value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>"
                                       placeholder="Ingresa tu nombre completo"></td>
                        </tr>
                        <tr>
                            <td><label for="correo">📧 Correo electrónico:</label></td>
                            <td><input type="email" name="correo" id="correo" maxlength="100" required 
                                       value="<?php echo isset($_POST['correo']) ? htmlspecialchars($_POST['correo']) : ''; ?>"
                                       placeholder="ejemplo@correo.com"></td>
                        </tr>
                        <tr>
                            <td><label for="password">🔒 Contraseña:</label></td>
                            <td>
                                <input type="password" name="password" id="password" required minlength="8" placeholder="Mínimo 8 caracteres">
                                <small>Mínimo 8 caracteres</small>
                            </td>
                        </tr>
                        <tr>
                            <td><label for="confirmar_password">🔒 Confirmar contraseña:</label></td>
                            <td><input type="password" name="confirmar_password" id="confirmar_password" required minlength="8" placeholder="Confirma tu contraseña"></td>
                        </tr>
                        <tr>
                            <td colspan="2" class="button-row">
                                <input type="submit" name="registro" value="🔐 Registrar Usuario">
                                <input type="reset" value="🔄 Limpiar">
                            </td>
                        </tr>
                    </table>
                </form>
            </div>
        </div>

        <hr>
        
        <div class="info-section">
            <h3>🛡️ Información de Seguridad</h3>
            <ul>
                <li>Se generarán claves RSA de 2048 bits automáticamente</li>
                <li>Las contraseñas se almacenan usando cifrado bcrypt</li>
                <li>Cada usuario tiene un par de claves único</li>
                <li>Las claves privadas están protegidas en la base de datos</li>
            </ul>
        </div>

        <nav>
            <p><a href="login.php">🔑 Ya tengo cuenta - Iniciar Sesión</a></p>
            <p><a href="index.php">🏠 Volver al Inicio</a></p>
        </nav>
    </div>
</body>
</html>