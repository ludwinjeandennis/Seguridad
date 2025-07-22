<?php session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Encriptación - Inicio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #2563eb;
            --primary-hover: #1d4ed8;
            --secondary-color: #64748b;
            --success-color: #059669;
            --warning-color: #d97706;
            --error-color: #dc2626;
            --background: #0f172a;
            --surface-1: #1e293b;
            --surface-2: #334155;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-light: #f1f5f9;
            --text-muted: #94a3b8;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --gradient-primary: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-secondary: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-hero: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #f093fb 100%);
            --gradient-dark: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--gradient-dark);
            color: var(--text-light);
            line-height: 1.6;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        /* Animated background particles */
        .particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }

        .particle {
            position: absolute;
            width: 3px;
            height: 3px;
            background: rgba(102, 126, 234, 0.6);
            border-radius: 50%;
            animation: float 6s infinite linear;
        }

        .particle:nth-child(2n) {
            background: rgba(240, 147, 251, 0.4);
            animation-duration: 8s;
            animation-delay: -2s;
        }

        .particle:nth-child(3n) {
            background: rgba(118, 75, 162, 0.5);
            animation-duration: 10s;
            animation-delay: -4s;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
            position: relative;
            z-index: 2;
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            position: relative;
        }

        .hero-content {
            max-width: 800px;
            animation: fadeInUp 1s ease-out;
        }

        .hero-icon {
            font-size: 5rem;
            margin-bottom: 2rem;
            background: var(--gradient-hero);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            animation: pulse 2s infinite;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 800;
            margin-bottom: 1.5rem;
            background: var(--gradient-hero);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            line-height: 1.2;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 3rem;
            font-weight: 400;
        }

        .status-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            padding: 2.5rem;
            margin: 2rem 0;
            box-shadow: var(--shadow-xl);
            position: relative;
            overflow: hidden;
        }

        .status-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: var(--gradient-hero);
        }

        .welcome-message {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 2rem;
            color: var(--text-light);
        }

        .welcome-description {
            font-size: 1rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            line-height: 1.8;
        }

        .action-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .action-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .action-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.6s ease;
        }

        .action-card:hover::before {
            left: 100%;
        }

        .action-card:hover {
            transform: translateY(-8px);
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(102, 126, 234, 0.5);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .action-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            background: var(--gradient-primary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .action-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text-light);
        }

        .action-description {
            font-size: 0.9rem;
            color: var(--text-muted);
            margin-bottom: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 2rem;
            border: none;
            border-radius: 12px;
            font-size: 0.95rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
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
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.6);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: var(--text-light);
            border: 1px solid rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
        }

        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
        }

        .login-prompt {
            text-align: center;
            animation: fadeInUp 1s ease-out 0.3s both;
        }

        .login-prompt p {
            font-size: 1.125rem;
            margin-bottom: 2rem;
            color: var(--text-muted);
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin: 3rem 0;
        }

        .feature-item {
            text-align: center;
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 16px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            transition: all 0.3s ease;
        }

        .feature-item:hover {
            transform: translateY(-4px);
            background: rgba(255, 255, 255, 0.1);
        }

        .feature-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: var(--gradient-secondary);
            background-clip: text;
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .feature-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--text-light);
        }

        .feature-description {
            font-size: 0.9rem;
            color: var(--text-muted);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-icon {
                font-size: 4rem;
            }
            
            .status-card {
                padding: 1.5rem;
                margin: 1rem 0;
            }
            
            .action-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .features-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 0 1rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
            
            .hero-subtitle {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Animated Background Particles -->
    <div class="particles">
        <div class="particle" style="left: 10%; animation-delay: 0s;"></div>
        <div class="particle" style="left: 20%; animation-delay: -1s;"></div>
        <div class="particle" style="left: 30%; animation-delay: -2s;"></div>
        <div class="particle" style="left: 40%; animation-delay: -3s;"></div>
        <div class="particle" style="left: 50%; animation-delay: -4s;"></div>
        <div class="particle" style="left: 60%; animation-delay: -5s;"></div>
        <div class="particle" style="left: 70%; animation-delay: -6s;"></div>
        <div class="particle" style="left: 80%; animation-delay: -7s;"></div>
        <div class="particle" style="left: 90%; animation-delay: -8s;"></div>
    </div>

    <div class="container">
        <section class="hero-section">
            <div class="hero-content">
                <div class="hero-icon">🔐</div>
                <h1 class="hero-title">Sistema de Encriptación</h1>
                <p class="hero-subtitle">Proteja sus datos con algoritmos de cifrado RSA y César implementados desde cero</p>

                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <div class="status-card">
                        <div class="welcome-message">
                            🎉 ¡Bienvenido de nuevo!
                        </div>
                        <p class="welcome-description">
                            Su sesión está activa y puede acceder a todas las funcionalidades del sistema de encriptación. 
                            Explore nuestras herramientas de cifrado RSA y César, revise las políticas de seguridad o gestione su cuenta.
                        </p>

                        <div class="action-grid">
                            <div class="action-card">
                                <div class="action-icon">🚀</div>
                                <h3 class="action-title">Panel Principal</h3>
                                <p class="action-description">Acceda al dashboard completo con todas las herramientas de cifrado</p>
                                <a href="dashboard.php" class="btn btn-primary">
                                    <span>Ir al Dashboard</span>
                                    <span>→</span>
                                </a>
                            </div>

                            <div class="action-card">
                                <div class="action-icon">📋</div>
                                <h3 class="action-title">Políticas</h3>
                                <p class="action-description">Consulte las políticas de seguridad y mejores prácticas</p>
                                <a href="politicas.php" class="btn btn-secondary">
                                    <span>Ver Políticas</span>
                                    <span>📖</span>
                                </a>
                            </div>

                            <div class="action-card">
                                <div class="action-icon">🚪</div>
                                <h3 class="action-title">Cerrar Sesión</h3>
                                <p class="action-description">Termine su sesión de forma segura cuando haya terminado</p>
                                <a href="logout.php" class="btn btn-secondary">
                                    <span>Cerrar Sesión</span>
                                    <span>🔓</span>
                                </a>
                            </div>
                        </div>
                    </div>

                <?php else: ?>
                    <div class="status-card">
                        <div class="login-prompt">
                            <p>Inicie sesión para acceder a las funcionalidades completas de encriptación</p>
                            <a href="login.php" class="btn btn-primary">
                                <span>🔑</span>
                                <span>Iniciar Sesión</span>
                            </a>
                        </div>

                        <div class="features-grid">
                            <div class="feature-item">
                                <div class="feature-icon">🔒</div>
                                <h4 class="feature-title">Cifrado RSA</h4>
                                <p class="feature-description">Algoritmo de clave pública implementado manualmente</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">📜</div>
                                <h4 class="feature-title">Cifrado César</h4>
                                <p class="feature-description">Método histórico de sustitución por desplazamiento</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">🛡️</div>
                                <h4 class="feature-title">Seguridad</h4>
                                <p class="feature-description">Gestión segura de claves y políticas robustas</p>
                            </div>
                            <div class="feature-item">
                                <div class="feature-icon">📊</div>
                                <h4 class="feature-title">Historial</h4>
                                <p class="feature-description">Seguimiento completo de actividades de cifrado</p>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    </div>
</body>
</html>