<?php
declare(strict_types=1);
/**
 * Vista de Login — Tema Oscuro.
 * Variables: $error (string|null)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #0a0a1a;
            --bg-card: rgba(255, 255, 255, 0.04);
            --border: rgba(255, 255, 255, 0.08);
            --text-primary: #e8e8f0;
            --text-secondary: #9090b0;
            --text-muted: #606080;
            --accent: #6c5ce7;
            --accent-light: #a29bfe;
            --accent-glow: rgba(108, 92, 231, 0.3);
            --cyan: #00cec9;
            --danger: #e17055;
            --danger-bg: rgba(225, 112, 85, 0.12);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-primary);
        }

        .login-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }

        .login-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 40px 32px;
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }

        .login-header {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8em;
            margin-bottom: 16px;
            box-shadow: 0 8px 25px var(--accent-glow);
        }

        .login-header h1 {
            font-size: 1.5em;
            font-weight: 700;
            margin-bottom: 4px;
        }

        .login-header p {
            color: var(--text-secondary);
            font-size: 0.88em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.85em;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .form-group input {
            width: 100%;
            padding: 12px 14px;
            background: rgba(255,255,255,0.06);
            border: 1px solid var(--border);
            border-radius: 12px;
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 0.92em;
            transition: all 0.3s;
        }

        .form-group input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .btn-login {
            width: 100%;
            padding: 13px;
            background: linear-gradient(135deg, var(--accent), #8b7cf7);
            color: white;
            border: none;
            border-radius: 12px;
            font-family: 'Inter', sans-serif;
            font-size: 0.95em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px var(--accent-glow);
            margin-top: 8px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px var(--accent-glow);
        }

        .error-message {
            background: var(--danger-bg);
            color: var(--danger);
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 0.85em;
            margin-bottom: 20px;
            border: 1px solid rgba(225, 112, 85, 0.2);
            text-align: center;
        }

        .login-footer {
            text-align: center;
            margin-top: 24px;
            color: var(--text-muted);
            font-size: 0.78em;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-card { animation: fadeIn 0.5s ease-out; }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">📦</div>
                <h1><?= APP_NAME ?></h1>
                <p>Sistema de Inventario</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="error-message">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>?controller=auth&action=login">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '') ?>">

                <div class="form-group">
                    <label>Usuario</label>
                    <input type="text" name="username" placeholder="Ingresa tu usuario" required autofocus>
                </div>

                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="Ingresa tu contraseña" required>
                </div>

                <button type="submit" class="btn-login">Iniciar Sesión</button>
            </form>

            <div class="login-footer">
                Tecnología Web II — <?= date('Y') ?>
            </div>
        </div>
    </div>
</body>
</html>
