<?php
/**
 * Formulario de acceso al sistema
 * Variables disponibles:
 *   - error (string) mensaje de error opcional
 */
function h($s){return htmlspecialchars($s,ENT_QUOTES,'UTF-8');}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión - <?= h(APP_NAME) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        /* Animación de fondo */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            animation: float 20s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes float {
            0%, 100% { transform: translate(0, 0); }
            50% { transform: translate(25px, 25px); }
        }

        .login-container {
            position: relative;
            width: 100%;
            max-width: 420px;
            padding: 20px;
            animation: slideIn 0.6s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 50px 30px 40px;
            text-align: center;
        }

        .login-icon {
            font-size: 48px;
            margin-bottom: 15px;
            display: inline-block;
            animation: bounce 2s ease-in-out infinite;
        }

        @keyframes bounce {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .login-header h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .login-header p {
            font-size: 14px;
            opacity: 0.9;
            font-weight: 500;
        }

        .login-body {
            padding: 40px 30px;
        }

        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #2c3e50;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 15px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-group input::placeholder {
            color: #a0a0a0;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group input:hover {
            border-color: #d0d0d0;
        }

        .input-icon {
            position: absolute;
            right: 14px;
            top: 44px;
            color: #a0a0a0;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .form-group input:focus ~ .input-icon,
        .form-group input:not(:placeholder-shown) ~ .input-icon {
            color: #667eea;
        }

        .submit-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
            letter-spacing: 0.5px;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
        }

        .submit-btn:active {
            transform: translateY(0);
        }

        .error-message {
            background: #fee;
            color: #c33;
            border: 2px solid #fcc;
            border-radius: 10px;
            padding: 14px 16px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 500;
            font-size: 14px;
            animation: shake 0.5s ease;
        }

        .error-message::before {
            content: '⚠️';
            font-size: 18px;
            flex-shrink: 0;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .login-footer {
            padding: 20px 30px;
            background: #f8f9fa;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        /* Responsive */
        @media (max-width: 480px) {
            .login-header {
                padding: 40px 20px 30px;
            }

            .login-body {
                padding: 30px 20px;
            }

            .login-header h1 {
                font-size: 24px;
            }

            .login-icon {
                font-size: 40px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="login-icon">🔐</div>
                <h1><?= h(APP_NAME) ?></h1>
                <p>Sistema de Inventario</p>
            </div>

            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="error-message"><?= h($error) ?></div>
                <?php endif; ?>

                <form method="post" action="login.php">
                    <div class="form-group">
                        <label for="username">Usuario</label>
                        <input 
                            type="text" 
                            id="username"
                            name="username" 
                            placeholder="Ingresa tu usuario"
                            required 
                            autofocus
                        >
                        <span class="input-icon">👤</span>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña</label>
                        <input 
                            type="password" 
                            id="password"
                            name="password" 
                            placeholder="Ingresa tu contraseña"
                            required
                        >
                        <span class="input-icon">🔑</span>
                    </div>

                    <button class="submit-btn" type="submit">Acceder</button>
                </form>
            </div>

            <div class="login-footer">
                Sistema seguro de inventario de hardware • v1.0
            </div>
        </div>
    </div>
</body>
</html>
