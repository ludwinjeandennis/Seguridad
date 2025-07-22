<?php
// includes/db.php
$conexion = new mysqli("localhost", "root", "", "seguridad_web");
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}
