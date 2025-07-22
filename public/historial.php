<?php
// public/historial.php
session_start();
require_once '../includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$nombre = $_SESSION['nombre'];

// Crear tabla de log si no existe
$conexion->query("CREATE TABLE IF NOT EXISTS log_actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    accion VARCHAR(255) NOT NULL,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
)");

// Obtener historial de actividades
$stmt = $conexion->prepare("SELECT accion, fecha FROM log_actividades WHERE usuario_id = ? ORDER BY fecha DESC LIMIT 50");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Historial de Actividades</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            /* Sombras y gradientes */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        .header {
            background: var(--gradient-primary);
            padding: 3rem 0;
            margin: -2rem -1rem 3rem -1rem;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(50%, -50%);
        }

        .header h1 {
            color: white;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header h2 {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.2rem;
            font-weight: 400;
        }

        .table-container {
            background: var(--card-bg);
            border-radius: 16px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            margin-bottom: 2rem;
            position: relative;
        }

        .table-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .table-header {
            padding: 1.5rem 2rem;
            border-bottom: 2px solid var(--border-color);
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        }

        .table-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table thead th {
            background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
            padding: 1rem 2rem;
            text-align: left;
            font-weight: 600;
            color: var(--text-primary);
            border-bottom: 2px solid var(--border-color);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            transform: translateY(-1px);
        }

        .table tbody td {
            padding: 1.25rem 2rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .activity-text {
            color: var(--text-primary);
            font-weight: 500;
        }

        .date-text {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
            color: var(--text-secondary);
        }

        .empty-state .icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .navigation {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            background: var(--gradient-primary);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
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
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }

        .btn:hover::before {
            left: 100%;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: var(--card-bg);
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--gradient-primary);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.25rem;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 2rem;
            }
            
            .table thead th,
            .table tbody td {
                padding: 0.75rem 1rem;
            }
            
            .btn {
                padding: 0.75rem 1.25rem;
                font-size: 0.9rem;
            }
            
            .container {
                padding: 1rem;
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-container {
            animation: slideIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📊 Historial de Actividades</h1>
            <h2>Usuario: <?php echo htmlspecialchars($nombre); ?></h2>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?php echo $resultado->num_rows; ?></div>
                <div class="stat-label">Actividades Recientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">50</div>
                <div class="stat-label">Límite de Registros</div>
            </div>
        </div>

        <div class="table-container">
            <div class="table-header">
                <h3>Registro de Actividades</h3>
            </div>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>Actividad</th>
                        <th>Fecha y Hora</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($resultado->num_rows > 0): ?>
                        <?php while ($fila = $resultado->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <span class="activity-text">
                                        <?php echo htmlspecialchars($fila['accion']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="date-text">
                                        <?php echo date('d/m/Y H:i:s', strtotime($fila['fecha'])); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="2">
                                <div class="empty-state">
                                    <div class="icon">📝</div>
                                    <p>No hay actividades registradas</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="navigation">
            <a href="dashboard.php" class="btn">
                🔙 Volver al Dashboard
            </a>
        </div>
    </div>
</body>
</html>

<?php
$stmt->close();
?>