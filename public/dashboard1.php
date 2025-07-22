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
<html>
<head>
    <title>Panel de Seguridad - Sistema de Encriptación</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .mensaje { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .error { background-color: #ffebee; color: #c62828; border: 1px solid #f8bbd9; }
        .info { background-color: #e8f5e8; color: #2e7d2e; border: 1px solid #a5d6a7; }
        .resultado { background-color: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
        textarea { width: 100%; box-sizing: border-box; }
        .metodo { border: 1px solid #ddd; padding: 15px; margin: 10px 0; border-radius: 5px; }
        .btn { padding: 8px 15px; margin: 5px; cursor: pointer; }
        .btn-primary { background-color: #007bff; color: white; border: none; }
        .btn-warning { background-color: #ffc107; color: black; border: none; }
        .btn-danger { background-color: #dc3545; color: white; border: none; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 Panel de Seguridad</h1>
        <h2>Bienvenido, <?php echo htmlspecialchars($nombre); ?>!</h2>

        <?php if ($error): ?>
            <div class="mensaje error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($info): ?>
            <div class="mensaje info"><?php echo htmlspecialchars($info); ?></div>
        <?php endif; ?>

        <!-- Sistema RSA -->
        <div class="metodo">
            <h3>🔒 Sistema de Cifrado RSA (Implementación Manual)</h3>
            <p><strong>Descripción:</strong> Algoritmo de clave asimétrica implementado desde cero sin librerías externas.</p>
            
            <form method="post">
                <label for="texto">Ingrese el texto:</label><br>
                <textarea name="texto" id="texto" rows="4" cols="60" placeholder="Escriba aquí su mensaje..."></textarea><br><br>
                
                <input type="submit" name="cifrar" value="🔒 Cifrar con RSA" class="btn btn-primary">
                <input type="submit" name="descifrar" value="🔓 Descifrar con RSA" class="btn btn-primary">
            </form>

            <?php if ($mensaje_cifrado): ?>
                <div class="resultado">
                    <h4>📝 Mensaje Cifrado (RSA):</h4>
                    <textarea rows="4" cols="60" readonly><?php echo htmlspecialchars($mensaje_cifrado); ?></textarea>
                    <br><small>Copie este texto cifrado para enviarlo de forma segura</small>
                </div>
            <?php endif; ?>

            <?php if ($mensaje_descifrado): ?>
                <div class="resultado">
                    <h4>📖 Mensaje Descifrado (RSA):</h4>
                    <textarea rows="4" cols="60" readonly><?php echo htmlspecialchars($mensaje_descifrado); ?></textarea>
                </div>
            <?php endif; ?>
        </div>

        <!-- Sistema César -->
        <div class="metodo">
            <h3>📜 Cifrado César (Método Histórico)</h3>
            <p><strong>Descripción:</strong> Cifrado por sustitución simple usado por Julio César. Desplaza cada letra un número fijo de posiciones.</p>
            
            <form method="post">
                <label for="texto2">Ingrese el texto:</label><br>
                <textarea name="texto" rows="4" cols="60" placeholder="Texto para cifrado César..."></textarea><br>
                
                <label for="desplazamiento">Desplazamiento (1-25):</label>
                <input type="number" name="desplazamiento" value="3" min="1" max="25" style="width: 60px;"><br><br>
                
                <input type="submit" name="cifrar_cesar" value="🔒 Cifrar César" class="btn btn-warning">
                <input type="submit" name="descifrar_cesar" value="🔓 Descifrar César" class="btn btn-warning">
            </form>

            <?php if ($mensaje_cesar): ?>
                <div class="resultado">
                    <h4>📝 Mensaje Cifrado (César):</h4>
                    <textarea rows="3" cols="60" readonly><?php echo htmlspecialchars($mensaje_cesar); ?></textarea>
                </div>
            <?php endif; ?>

            <?php if ($mensaje_descifrado_cesar): ?>
                <div class="resultado">
                    <h4>📖 Mensaje Descifrado (César):</h4>
                    <textarea rows="3" cols="60" readonly><?php echo htmlspecialchars($mensaje_descifrado_cesar); ?></textarea>
                </div>
            <?php endif; ?>
        </div>

        <!-- Gestión de Claves RSA -->
        <div class="metodo">
            <h3>🔧 Gestión de Claves RSA</h3>
            <form method="post" onsubmit="return confirm('¿Está seguro? Esto invalidará todos los mensajes cifrados anteriormente.')">
                <input type="submit" name="regenerar_claves" value="🔄 Regenerar Claves RSA" class="btn btn-danger">
                <small>⚠️ Precaución: Al regenerar las claves, no podrá descifrar mensajes antiguos</small>
            </form>

            <h4>📊 Información de Claves Actuales:</h4>
            <details>
                <summary>Ver Información de Clave Pública</summary>
                <pre><?php echo mostrarInfoClave($clavePublica); ?></pre>
            </details>
            
            <details>
                <summary>Ver Información de Clave Privada</summary>
                <pre><?php echo mostrarInfoClave($clavePrivada); ?></pre>
            </details>
        </div>

        <hr>

        <nav>
            <h3>🧭 Navegación</h3>
            <ul>
                <li><a href="politicas.php">📋 Ver Políticas de Seguridad</a></li>
                <li><a href="historial.php">📊 Historial de Actividades</a></li>
                <li><a href="logout.php">🚪 Cerrar Sesión</a></li>
            </ul>
        </nav>
    </div>
</body>
</html>