<?php
// public/politicas.php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Políticas de Seguridad</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
            font-family: 'Inter', sans-serif;
            background: var(--background);
            color: var(--text-primary);
            line-height: 1.6;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            background: var(--gradient-primary);
            color: white;
            text-align: center;
            padding: 4rem 2rem;
            margin-bottom: 3rem;
            border-radius: 16px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, transparent 50%);
            transform: rotate(-45deg);
        }

        .header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
            position: relative;
            z-index: 1;
        }

        .header p {
            font-size: 1.1rem;
            opacity: 0.9;
            position: relative;
            z-index: 1;
        }

        .policies-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(500px, 1fr));
            gap: 2rem;
            margin-bottom: 3rem;
        }

        .policy-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: var(--shadow-md);
            border-top: 4px solid;
            border-image: var(--gradient-primary) 1;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .policy-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--gradient-primary);
        }

        .policy-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .policy-card h3 {
            color: var(--primary-color);
            font-size: 1.4rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .policy-icon {
            width: 24px;
            height: 24px;
            background: var(--gradient-primary);
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            color: white;
            font-weight: 600;
        }

        .policy-card ul {
            list-style: none;
            padding: 0;
        }

        .policy-card li {
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--border-color);
            position: relative;
            padding-left: 1.5rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .policy-card li:last-child {
            border-bottom: none;
        }

        .policy-card li::before {
            content: '✓';
            position: absolute;
            left: 0;
            top: 0.75rem;
            color: var(--success-color);
            font-weight: 600;
            font-size: 0.9rem;
        }

        .policy-card li:hover {
            color: var(--text-primary);
        }

        .back-section {
            text-align: center;
            margin-top: 3rem;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: var(--gradient-primary);
            color: white;
            text-decoration: none;
            padding: 1rem 2rem;
            border-radius: 12px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-md);
            position: relative;
            overflow: hidden;
        }

        .back-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .back-button:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .back-button:hover::before {
            left: 100%;
        }

        .security-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: linear-gradient(135deg, var(--success-color), #10b981);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 50px;
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .security-badge::before {
            content: '🔒';
            font-size: 1rem;
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

        .policy-card {
            animation: slideIn 0.6s ease forwards;
        }

        .policy-card:nth-child(1) { animation-delay: 0.1s; }
        .policy-card:nth-child(2) { animation-delay: 0.2s; }
        .policy-card:nth-child(3) { animation-delay: 0.3s; }
        .policy-card:nth-child(4) { animation-delay: 0.4s; }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .header {
                padding: 2.5rem 1.5rem;
                margin-bottom: 2rem;
            }
            
            .header h1 {
                font-size: 2rem;
            }
            
            .policies-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }
            
            .policy-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Políticas de Seguridad</h1>
            <p>Marco de seguridad implementado en el sistema</p>
        </div>

        <div class="security-badge">
            Sistema Seguro Verificado
        </div>

        <div class="policies-grid">
            <div class="policy-card">
                <h3>
                    <span class="policy-icon">🔐</span>
                    Política de Contraseñas
                </h3>
                <ul>
                    <li>Contraseñas con mínimo 8 caracteres para garantizar complejidad básica</li>
                    <li>Uso de bcrypt para almacenamiento seguro con hash y salt</li>
                    <li>Verificación de fortaleza en tiempo real</li>
                    <li>Expiración periódica recomendada cada 90 días</li>
                </ul>
            </div>

            <div class="policy-card">
                <h3>
                    <span class="policy-icon">🔑</span>
                    Gestión de Claves
                </h3>
                <ul>
                    <li>Generación automática con OpenSSL utilizando 2048 bits RSA</li>
                    <li>Claves privadas almacenadas cifradas en la base de datos</li>
                    <li>Rotación automática de claves según políticas establecidas</li>
                    <li>Separación entre claves de cifrado y firma digital</li>
                </ul>
            </div>

            <div class="policy-card">
                <h3>
                    <span class="policy-icon">👤</span>
                    Control de Acceso
                </h3>
                <ul>
                    <li>Control de sesión mediante ID único y seguro</li>
                    <li>Regeneración de sesión automática tras el login exitoso</li>
                    <li>Timeout de sesión por inactividad prolongada</li>
                    <li>Validación de permisos por cada operación crítica</li>
                </ul>
            </div>

            <div class="policy-card">
                <h3>
                    <span class="policy-icon">💾</span>
                    Respaldo y Recuperación
                </h3>
                <ul>
                    <li>Respaldo periódico automatizado de claves y base de datos</li>
                    <li>Almacenamiento de claves fuera del entorno público del servidor</li>
                    <li>Procedimientos de recuperación ante desastres documentados</li>
                    <li>Verificación de integridad de respaldos mediante checksums</li>
                </ul>
            </div>
        </div>

        <div class="back-section">
            <a href="dashboard.php" class="back-button">
                ← Regresar al Dashboard
            </a>
        </div>
    </div>
</body>
</html>