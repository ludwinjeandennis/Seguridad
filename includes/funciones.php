<?php
// includes/funciones.php
require_once 'rsa_manual.php';

function generarClavesRSA() {
    $claves = RSAManual::generarClaves();
    
    return [
        'privada' => RSAManual::serializarClave($claves['privada']),
        'publica' => RSAManual::serializarClave($claves['publica'])
    ];
}

function cifrarRSA($data, $clavePublicaSerializada) {
    try {
        $clavePublica = RSAManual::deserializarClave($clavePublicaSerializada);
        if (!$clavePublica) {
            return false;
        }
        
        return RSAManual::cifrarTexto($data, $clavePublica);
    } catch (Exception $e) {
        return false;
    }
}

function descifrarRSA($dataCifrada, $clavePrivadaSerializada) {
    try {
        $clavePrivada = RSAManual::deserializarClave($clavePrivadaSerializada);
        if (!$clavePrivada) {
            return false;
        }
        
        return RSAManual::descifrarTexto($dataCifrada, $clavePrivada);
    } catch (Exception $e) {
        return false;
    }
}

function hashConSal($password) {
    // Implementación manual de hash con sal
    $sal = generarSalAleatoria();
    $hash = hash('sha256', $sal . $password);
    return $sal . ':' . $hash;
}

function verificarPassword($password, $hashCompleto) {
    $partes = explode(':', $hashCompleto);
    if (count($partes) != 2) {
        return false;
    }
    
    $sal = $partes[0];
    $hashGuardado = $partes[1];
    $hashCalculado = hash('sha256', $sal . $password);
    
    return $hashCalculado === $hashGuardado;
}

function generarSalAleatoria($longitud = 16) {
    $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $sal = '';
    for ($i = 0; $i < $longitud; $i++) {
        $sal .= $caracteres[mt_rand(0, strlen($caracteres) - 1)];
    }
    return $sal;
}

function sanitizarEntrada($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function validarPassword($password) {
    // Validar que la contraseña tenga al menos 8 caracteres
    if (strlen($password) < 8) {
        return false;
    }
    return true;
}

function logActividad($usuario_id, $accion, $conexion) {
    // Crear tabla si no existe
    $sql = "CREATE TABLE IF NOT EXISTS log_actividades (
        id INT AUTO_INCREMENT PRIMARY KEY,
        usuario_id INT NOT NULL,
        accion VARCHAR(255) NOT NULL,
        fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $conexion->query($sql);
    
    // Insertar actividad
    $stmt = $conexion->prepare("INSERT INTO log_actividades (usuario_id, accion, fecha) VALUES (?, ?, NOW())");
    if ($stmt) {
        $stmt->bind_param("is", $usuario_id, $accion);
        $stmt->execute();
        $stmt->close();
    }
}

// Función para generar un ID único para sesiones
function generarIdSesion() {
    return bin2hex(random_bytes(32));
}

// Función para validar email manualmente
function validarEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Función de cifrado simple adicional (César) para demostración
function cifradoCesar($texto, $desplazamiento) {
    $resultado = '';
    $longitud = strlen($texto);
    
    for ($i = 0; $i < $longitud; $i++) {
        $char = $texto[$i];
        
        if (ctype_alpha($char)) {
            $ascii = ord($char);
            $base = ctype_upper($char) ? ord('A') : ord('a');
            $char = chr((($ascii - $base + $desplazamiento) % 26) + $base);
        }
        
        $resultado .= $char;
    }
    
    return $resultado;
}

function descifradoCesar($texto, $desplazamiento) {
    return cifradoCesar($texto, -$desplazamiento);
}

// Función para mostrar información de claves RSA
function mostrarInfoClave($claveSerializada) {
    $clave = RSAManual::deserializarClave($claveSerializada);
    if ($clave) {
        return "n: " . $clave['n'] . "\ne o d: " . (isset($clave['e']) ? $clave['e'] : $clave['d']);
    }
    return "Error al leer la clave";
}
?>