<?php
declare(strict_types=1);
/**
 * Layout base del sistema — Tema Oscuro Premium.
 * Variables esperadas: $titulo (string), $contenido (string path de la vista).
 */
$nombreUsuario = htmlspecialchars($_SESSION['usuario_nombre'] ?? '', ENT_QUOTES, 'UTF-8');
$rolUsuario = $_SESSION['usuario_rol'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'InveManager') ?> — <?= APP_NAME ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* ══════════════════════════════════════════════════════
           DESIGN SYSTEM — Tema Oscuro Premium
           ══════════════════════════════════════════════════════ */
        :root {
            --bg-primary: #0a0a1a;
            --bg-secondary: #12122a;
            --bg-card: rgba(255, 255, 255, 0.04);
            --bg-card-hover: rgba(255, 255, 255, 0.07);
            --bg-input: rgba(255, 255, 255, 0.06);
            --border: rgba(255, 255, 255, 0.08);
            --border-focus: #6c5ce7;
            --text-primary: #e8e8f0;
            --text-secondary: #9090b0;
            --text-muted: #606080;
            --accent: #6c5ce7;
            --accent-light: #a29bfe;
            --accent-glow: rgba(108, 92, 231, 0.3);
            --cyan: #00cec9;
            --cyan-glow: rgba(0, 206, 201, 0.25);
            --success: #00b894;
            --success-bg: rgba(0, 184, 148, 0.12);
            --warning: #fdcb6e;
            --warning-bg: rgba(253, 203, 110, 0.12);
            --danger: #e17055;
            --danger-bg: rgba(225, 112, 85, 0.12);
            --info-bg: rgba(108, 92, 231, 0.12);
            --radius: 12px;
            --radius-lg: 16px;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            line-height: 1.6;
        }

        /* ── Navbar ───────────────────────────── */
        .navbar {
            background: var(--bg-secondary);
            border-bottom: 1px solid var(--border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 64px;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(20px);
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: var(--text-primary);
            font-weight: 700;
            font-size: 1.15em;
        }

        .navbar-brand .logo {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.9em;
        }

        .navbar-nav {
            display: flex;
            align-items: center;
            gap: 4px;
            list-style: none;
        }

        .navbar-nav a {
            color: var(--text-secondary);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.9em;
            font-weight: 500;
            transition: var(--transition);
        }

        .navbar-nav a:hover,
        .navbar-nav a.active {
            color: var(--text-primary);
            background: var(--bg-card-hover);
        }

        .navbar-user {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .navbar-user .user-info {
            text-align: right;
        }

        .navbar-user .user-name {
            font-size: 0.85em;
            font-weight: 600;
            color: var(--text-primary);
        }

        .navbar-user .user-role {
            font-size: 0.72em;
            color: var(--accent-light);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .navbar-user .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), var(--cyan));
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 0.8em;
            color: #fff;
        }

        /* ── Main Content ─────────────────────── */
        .main-content {
            max-width: 1300px;
            margin: 0 auto;
            padding: 28px 24px;
        }

        /* ── Cards ────────────────────────────── */
        .card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 24px;
            backdrop-filter: blur(10px);
        }

        /* ── Stats Grid ──────────────────────── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            padding: 20px;
            text-align: center;
            transition: var(--transition);
        }

        .stat-card:hover {
            border-color: var(--accent);
            box-shadow: 0 0 20px var(--accent-glow);
            transform: translateY(-2px);
        }

        .stat-card.warning { border-color: var(--danger); }
        .stat-card.warning:hover { box-shadow: 0 0 20px rgba(225, 112, 85, 0.3); }

        .stat-card .number {
            font-size: 2.2em;
            font-weight: 800;
            background: linear-gradient(135deg, var(--accent-light), var(--cyan));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .stat-card.warning .number {
            background: linear-gradient(135deg, var(--warning), var(--danger));
            -webkit-background-clip: text;
            background-clip: text;
        }

        .stat-card .label {
            font-size: 0.82em;
            color: var(--text-secondary);
            margin-top: 4px;
            font-weight: 500;
        }

        /* ── Table ────────────────────────────── */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius);
            border: 1px solid var(--border);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: var(--bg-secondary);
            color: var(--accent-light);
            padding: 14px 16px;
            text-align: left;
            font-size: 0.78em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border);
        }

        td {
            padding: 12px 16px;
            font-size: 0.88em;
            border-bottom: 1px solid var(--border);
            color: var(--text-primary);
        }

        tr:hover td {
            background: var(--bg-card-hover);
        }

        tr.stock-critico td {
            background: var(--danger-bg);
        }

        tr.stock-critico:hover td {
            background: rgba(225, 112, 85, 0.18);
        }

        /* ── Badges ───────────────────────────── */
        .badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.75em;
            font-weight: 600;
            letter-spacing: 0.3px;
        }

        .badge-success { background: var(--success-bg); color: var(--success); }
        .badge-warning { background: var(--warning-bg); color: var(--warning); }
        .badge-danger { background: var(--danger-bg); color: var(--danger); }
        .badge-info { background: var(--info-bg); color: var(--accent-light); }

        /* ── Buttons ──────────────────────────── */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 10px 20px;
            border: none;
            border-radius: var(--radius);
            font-family: 'Inter', sans-serif;
            font-size: 0.88em;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--accent), #8b7cf7);
            color: white;
            box-shadow: 0 4px 15px var(--accent-glow);
        }

        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 6px 25px var(--accent-glow);
        }

        .btn-secondary {
            background: var(--bg-card);
            color: var(--text-secondary);
            border: 1px solid var(--border);
        }

        .btn-secondary:hover {
            background: var(--bg-card-hover);
            color: var(--text-primary);
            border-color: var(--accent);
        }

        .btn-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid rgba(225, 112, 85, 0.2);
        }

        .btn-danger:hover {
            background: rgba(225, 112, 85, 0.2);
        }

        .btn-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.8em;
        }

        /* ── Forms ────────────────────────────── */
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

        .form-group .required {
            color: var(--danger);
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 14px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 0.9em;
            transition: var(--transition);
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .form-group input::placeholder {
            color: var(--text-muted);
        }

        .form-group select option {
            background: var(--bg-secondary);
            color: var(--text-primary);
        }

        .has-error input,
        .has-error select {
            border-color: var(--danger) !important;
        }

        .error-text {
            color: var(--danger);
            font-size: 0.8em;
            margin-top: 4px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }

        @media (max-width: 768px) {
            .form-grid { grid-template-columns: 1fr; }
            .navbar { padding: 0 12px; }
            .main-content { padding: 16px 12px; }
        }

        /* ── Section Headers ─────────────────── */
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .page-header h1 {
            font-size: 1.6em;
            font-weight: 700;
        }

        .page-header .subtitle {
            color: var(--text-secondary);
            font-size: 0.9em;
        }

        /* ── Search Bar ──────────────────────── */
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-bar input {
            flex: 1;
            padding: 11px 16px;
            background: var(--bg-input);
            border: 1px solid var(--border);
            border-radius: var(--radius);
            color: var(--text-primary);
            font-family: 'Inter', sans-serif;
            font-size: 0.9em;
            transition: var(--transition);
        }

        .search-bar input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--accent-glow);
        }

        .search-bar input::placeholder {
            color: var(--text-muted);
        }

        /* ── Alerts ───────────────────────────── */
        .alert {
            padding: 14px 20px;
            border-radius: var(--radius);
            margin-bottom: 20px;
            font-size: 0.9em;
            font-weight: 500;
        }

        .alert-success {
            background: var(--success-bg);
            color: var(--success);
            border: 1px solid rgba(0, 184, 148, 0.2);
        }

        .alert-danger {
            background: var(--danger-bg);
            color: var(--danger);
            border: 1px solid rgba(225, 112, 85, 0.2);
        }

        /* ── Specific Components ─────────────── */
        .tipo-selector {
            display: flex;
            gap: 12px;
            margin-bottom: 20px;
        }

        .tipo-option {
            flex: 1;
            padding: 18px;
            border: 1px solid var(--border);
            border-radius: var(--radius);
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            background: var(--bg-card);
        }

        .tipo-option:hover {
            border-color: var(--accent);
            background: var(--bg-card-hover);
        }

        .tipo-option.active {
            border-color: var(--accent);
            background: var(--info-bg);
            box-shadow: 0 0 15px var(--accent-glow);
        }

        .tipo-option .icon {
            font-size: 1.8em;
            margin-bottom: 6px;
        }

        .tipo-option .name {
            font-weight: 600;
            font-size: 0.9em;
        }

        .campos-especificos {
            background: var(--bg-card);
            padding: 20px;
            border-radius: var(--radius);
            border: 1px dashed var(--accent);
            margin-top: 8px;
        }

        .campos-especificos h3 {
            color: var(--accent-light);
            margin-bottom: 16px;
            font-size: 1em;
        }

        .hidden { display: none; }

        /* ── Checkbox Custom ──────────────────── */
        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            accent-color: var(--accent);
        }

        .checkbox-group label {
            margin-bottom: 0 !important;
            cursor: pointer;
        }

        /* ── Footer ──────────────────────────── */
        .footer {
            text-align: center;
            padding: 20px;
            color: var(--text-muted);
            font-size: 0.8em;
            border-top: 1px solid var(--border);
            margin-top: 40px;
        }

        /* ── Animations ──────────────────────── */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        /* ── Actions Bar ─────────────────────── */
        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 16px;
        }

        .btn-logout {
            background: transparent;
            color: var(--danger);
            border: 1px solid rgba(225, 112, 85, 0.3);
            padding: 6px 14px;
            font-size: 0.82em;
        }

        .btn-logout:hover {
            background: var(--danger-bg);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <a href="<?= BASE_URL ?>?controller=hardware&action=index" class="navbar-brand">
            <span class="logo">📦</span>
            <?= APP_NAME ?>
        </a>
        <ul class="navbar-nav">
            <li><a href="<?= BASE_URL ?>?controller=hardware&action=index">Catálogo</a></li>
            <li><a href="<?= BASE_URL ?>?controller=hardware&action=crear">Agregar</a></li>
            <?php if ($rolUsuario === 'admin'): ?>
                <li><a href="<?= BASE_URL ?>?controller=usuario&action=index">Usuarios</a></li>
            <?php endif; ?>
        </ul>
        <div class="navbar-user">
            <div class="user-info">
                <div class="user-name"><?= $nombreUsuario ?></div>
                <div class="user-role"><?= htmlspecialchars($rolUsuario) ?></div>
            </div>
            <div class="avatar"><?= mb_strtoupper(mb_substr($nombreUsuario, 0, 1)) ?></div>
            <a href="<?= BASE_URL ?>?controller=auth&action=logout" class="btn btn-logout" title="Cerrar sesión">Salir</a>
        </div>
    </nav>

    <!-- Content -->
    <main class="main-content fade-in">
        <?= $contenido ?? '' ?>
    </main>

    <!-- Footer -->
    <div class="footer">
        <?= APP_NAME ?> v<?= APP_VERSION ?> — Tecnología Web II • <?= date('Y') ?>
    </div>
</body>
</html>
