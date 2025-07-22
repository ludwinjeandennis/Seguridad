<?php
// public/dashboard.php
session_start();
require_once '../includes/db.php';
require_once '../includes/funciones.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION['nombre'];

// Obtener las claves del usuario
$stmt = $conexion->prepare("SELECT clave_privada, clave_publica FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($clavePrivada, $clavePublica);
$stmt->fetch();
$stmt->close();

$mensaje_cifrado = $mensaje_descifrado = $mensaje_cesar = $mensaje_descifrado_cesar = "";
$error = "";
$info = "";

// Cifrado RSA
if (isset($_POST['cifrar']) && !empty($_POST['texto'])) {
    $texto = sanitizarEntrada($_POST['texto']);
    $resultado = cifrarRSA($texto, $clavePublica);
    
    if ($resultado !== false) {
        $mensaje_cifrado = $resultado;
        logActividad($usuario_id, "Cifrado RSA de mensaje", $conexion);
        $info = "✅ Mensaje cifrado exitosamente con RSA";
    } else {
        $error = "❌ Error al cifrar el mensaje con RSA.";
    }
}

// Descifrado RSA
if (isset($_POST['descifrar']) && !empty($_POST['texto'])) {
    $texto = $_POST['texto'];
    $resultado = descifrarRSA($texto, $clavePrivada);
    
    if ($resultado !== false) {
        $mensaje_descifrado = $resultado;
        logActividad($usuario_id, "Descifrado RSA de mensaje", $conexion);
        $info = "✅ Mensaje descifrado exitosamente con RSA";
    } else {
        $error = "❌ Error al descifrar el mensaje. Verifique que el texto esté correctamente cifrado con RSA.";
    }
}

// Cifrado César (método adicional)
if (isset($_POST['cifrar_cesar']) && !empty($_POST['texto'])) {
    $texto = sanitizarEntrada($_POST['texto']);
    $desplazamiento = isset($_POST['desplazamiento']) ? intval($_POST['desplazamiento']) : 3;
    $mensaje_cesar = cifradoCesar($texto, $desplazamiento);
    logActividad($usuario_id, "Cifrado César con desplazamiento $desplazamiento", $conexion);
    $info = "✅ Mensaje cifrado con algoritmo César (desplazamiento: $desplazamiento)";
}

// Descifrado César
if (isset($_POST['descifrar_cesar']) && !empty($_POST['texto'])) {
    $texto = $_POST['texto'];
    $desplazamiento = isset($_POST['desplazamiento']) ? intval($_POST['desplazamiento']) : 3;
    $mensaje_descifrado_cesar = descifradoCesar($texto, $desplazamiento);
    logActividad($usuario_id, "Descifrado César con desplazamiento $desplazamiento", $conexion);
    $info = "✅ Mensaje descifrado con algoritmo César (desplazamiento: $desplazamiento)";
}

// Regenerar claves RSA
if (isset($_POST['regenerar_claves'])) {
    $nuevasClaves = generarClavesRSA();
    $stmt = $conexion->prepare("UPDATE usuarios SET clave_privada = ?, clave_publica = ? WHERE id = ?");
    $stmt->bind_param("ssi", $nuevasClaves['privada'], $nuevasClaves['publica'], $usuario_id);
    
    if ($stmt->execute()) {
        $clavePrivada = $nuevasClaves['privada'];
        $clavePublica = $nuevasClaves['publica'];
        logActividad($usuario_id, "Regeneración de claves RSA", $conexion);
        $mensaje_cifrado = $mensaje_descifrado = "";
        $info = "🔄 Claves RSA regeneradas exitosamente";
    } else {
        $error = "❌ Error al regenerar las claves RSA";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Seguridad - Sistema de Encriptación</title>
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
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-success: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            text-align: center;
            margin-bottom: 3rem;
            padding: 2rem 0;
            background: var(--gradient-primary);
            border-radius: 20px;
            color: white;
            box-shadow: var(--shadow-lg);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1000 100" fill="rgba(255,255,255,0.1)"><polygon points="0,100 1000,0 1000,100"/></svg>') bottom;
            background-size: cover;
        }

        .header-content {
            position: relative;
            z-index: 1;
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .header h2 {
            font-size: 1.25rem;
            font-weight: 400;
            opacity: 0.9;
        }

        .mensaje {
            padding: 1rem 1.5rem;
            margin: 1.5rem 0;
            border-radius: 12px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            box-shadow: var(--shadow-sm);
            border: 1px solid transparent;
            animation: slideIn 0.3s ease-out;
        }

        .mensaje.error {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
            color: var(--error-color);
            border-color: #fecaca;
        }

        .mensaje.info {
            background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
            color: var(--success-color);
            border-color: #bbf7d0;
        }

        .metodo {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .metodo::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .metodo:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .metodo h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-primary);
        }

        .metodo p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        textarea, input[type="text"], input[type="number"] {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            font-size: 0.95rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.2s ease;
            background: #fafbfc;
        }

        textarea:focus, input:focus {
            outline: none;
            border-color: var(--primary-color);
            background: white;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            margin: 0.25rem;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn-primary {
            background: var(--gradient-primary);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-warning {
            background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-warning:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
            box-shadow: var(--shadow-sm);
        }

        .btn-danger:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .resultado {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1.5rem;
            margin: 1.5rem 0;
            box-shadow: var(--shadow-sm);
        }

        .resultado h4 {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-primary);
        }

        .resultado textarea {
            background: white;
            border: 1px solid var(--border-color);
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.85rem;
        }

        .resultado small {
            color: var(--text-secondary);
            font-size: 0.8rem;
            font-style: italic;
        }

        details {
            margin: 1rem 0;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        summary {
            padding: 1rem;
            background: #f8fafc;
            cursor: pointer;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        summary:hover {
            background: #f1f5f9;
        }

        details[open] summary {
            background: var(--primary-color);
            color: white;
        }

        pre {
            padding: 1rem;
            background: white;
            font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
            font-size: 0.8rem;
            overflow-x: auto;
            border-top: 1px solid var(--border-color);
        }

        .navigation {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            margin-top: 3rem;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .navigation h3 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: var(--text-primary);
        }

        .nav-links {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            list-style: none;
        }

        .nav-links li a {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            color: var(--text-primary);
            text-decoration: none;
            border-radius: 12px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-links li a:hover {
            background: var(--gradient-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .input-group {
            display: flex;
            gap: 1rem;
            align-items: end;
        }

        .input-group input[type="number"] {
            width: 100px;
        }

        .warning-text {
            display: block;
            margin-top: 0.5rem;
            font-size: 0.8rem;
            color: var(--warning-color);
            font-style: italic;
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

        @media (max-width: 768px) {
            .container {
                padding: 1rem 0.5rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .metodo {
                padding: 1.5rem;
            }
            
            .input-group {
                flex-direction: column;
                align-items: stretch;
            }
            
            .input-group input[type="number"] {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header class="header">
            <div class="header-content">
                <h1>🔐 Panel de Seguridad</h1>
                <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h2>
            </div>
        </header>

        <?php if ($error): ?>
            <div class="mensaje error">
                <span>❌</span>
                <span><?php echo htmlspecialchars($error); ?></span>
            </div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="mensaje info">
                <span>✅</span>
                <span><?php echo htmlspecialchars($info); ?></span>
            </div>
        <?php endif; ?>

        <!-- Sistema RSA -->
        <div class="metodo">
            <h3>🔒 Sistema de Cifrado RSA</h3>
            <p><strong>Descripción:</strong> Algoritmo de clave asimétrica implementado desde cero sin librerías externas. Proporciona seguridad avanzada mediante criptografía de clave pública.</p>
            
            <form method="post">
                <div class="form-group">
                    <label for="texto">Ingrese el texto a procesar:</label>
                    <textarea name="texto" id="texto" placeholder="Escriba aquí su mensaje para cifrar o descifrar..."></textarea>
                </div>
                
                <div class="btn-group">
                    <button type="submit" name="cifrar" class="btn btn-primary">
                        🔒 Cifrar con RSA
                    </button>
                    <button type="submit" name="descifrar" class="btn btn-primary">
                        🔓 Descifrar con RSA
                    </button>
                </div>
            </form>

            <?php if ($mensaje_cifrado): ?>
                <div class="resultado">
                    <h4>📝 Mensaje Cifrado (RSA):</h4>
                    <textarea rows="4" readonly><?php echo htmlspecialchars($mensaje_cifrado); ?></textarea>
                    <br><small>💡 Copie este texto cifrado para enviarlo de forma segura</small>
                </div>
            <?php endif; ?>

            <?php if ($mensaje_descifrado): ?>
                <div class="resultado">
                    <h4>📖 Mensaje Descifrado (RSA):</h4>
                    <textarea rows="4" readonly><?php echo htmlspecialchars($mensaje_descifrado); ?></textarea>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sistema César -->
        <div class="metodo">
            <h3>📜 Cifrado César</h3>
            <p><strong>Descripción:</strong> Cifrado por sustitución simple usado por Julio César. Desplaza cada letra un número fijo de posiciones en el alfabeto. Ideal para demostración educativa.</p>
            
            <form method="post">
                <div class="form-group">
                    <label for="texto2">Ingrese el texto a procesar:</label>
                    <textarea name="texto" placeholder="Texto para cifrado César..."></textarea>
                </div>
                
                <div class="input-group">
                    <div class="form-group">
                        <label for="desplazamiento">Desplazamiento (1-25):</label>
                        <input type="number" name="desplazamiento" value="3" min="1" max="25">
                    </div>
                    <div class="btn-group">
                        <button type="submit" name="cifrar_cesar" class="btn btn-warning">
                            🔒 Cifrar César
                        </button>
                        <button type="submit" name="descifrar_cesar" class="btn btn-warning">
                            🔓 Descifrar César
                        </button>
                    </div>
                </div>
            </form>

            <?php if ($mensaje_cesar): ?>
                <div class="resultado">
                    <h4>📝 Mensaje Cifrado (César):</h4>
                    <textarea rows="3" readonly><?php echo htmlspecialchars($mensaje_cesar); ?></textarea>
                </div>
            <?php endif; ?>

            <?php if ($mensaje_descifrado_cesar): ?>
                <div class="resultado">
                    <h4>📖 Mensaje Descifrado (César):</h4>
                    <textarea rows="3" readonly><?php echo htmlspecialchars($mensaje_descifrado_cesar); ?></textarea>
                </div>
            <?php endif; ?>
        </div>

        <!-- Gestión de Claves RSA -->
        <div class="metodo">
            <h3>🔧 Gestión de Claves RSA</h3>
            <p><strong>Descripción:</strong> Administre sus claves de cifrado RSA. Regenerar claves mejora la seguridad pero invalidará mensajes cifrados anteriormente.</p>
            
            <form method="post" onsubmit="return confirm('¿Está seguro? Esto invalidará todos los mensajes cifrados anteriormente.')">
                <button type="submit" name="regenerar_claves" class="btn btn-danger">
                    🔄 Regenerar Claves RSA
                </button>
                <small class="warning-text">⚠️ Precaución: Al regenerar las claves, no podrá descifrar mensajes antiguos</small>
            </form>

            <div style="margin-top: 2rem;">
                <h4>📊 Información de Claves Actuales:</h4>
                <details>
                    <summary>🔑 Ver Información de Clave Pública</summary>
                    <pre><?php echo mostrarInfoClave($clavePublica); ?></pre>
                </details>
                
                <details>
                    <summary>🔒 Ver Información de Clave Privada</summary>
                    <pre><?php echo mostrarInfoClave($clavePrivada); ?></pre>
                </details>
            </div>
        </div>

        <nav class="navigation">
            <h3>🧭 Navegación</h3>
            <ul class="nav-links">
                <li><a href="politicas.php">📋 Políticas de Seguridad</a></li>
                <li><a href="historial.php">📊 Historial de Actividades</a></li>
                <li><a href="logout.php">🚪 Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>