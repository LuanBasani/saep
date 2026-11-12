<?php
$usuarioAutenticado = !empty($_SESSION['usuario_id']);
$usuarioLogado = $_SESSION['usuario_nome'] ?? 'Usuário';
$paginaAtual = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'VetSystem — Clínica Veterinária') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root { --ink: #20332f; --muted: #71827b; --green: #236b58; --green-dark: #195343; --mint: #e6f1eb; --paper: #f5f4ee; --white: #fffefa; --line: #e4e5dc; --coral: #bd634f; }
        * { box-sizing: border-box; }
        body { font-family: 'DM Sans', sans-serif; background: var(--paper); margin: 0; color: var(--ink); min-height: 100vh; }
        h1, h2, h3, strong, .stat-box strong { font-family: 'Manrope', sans-serif; }
        .app-shell { min-height: 100vh; display: flex; }
        .sidebar { width: 248px; flex: 0 0 248px; background: var(--white); border-right: 1px solid var(--line); padding: 25px 17px; display: flex; flex-direction: column; }
        .brand { padding: 4px 12px 30px; color: var(--green); font: 800 1.15rem 'Manrope', sans-serif; letter-spacing: -.05em; }
        .brand small { display: block; margin-top: 4px; color: var(--muted); font: 600 .68rem 'DM Sans', sans-serif; letter-spacing: .1em; text-transform: uppercase; }
        .nav-label { padding: 0 12px; margin: 8px 0 10px; color: var(--muted); font-size: .67rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; }
        .side-nav { display: grid; gap: 6px; }
        .side-nav a { display: flex; align-items: center; gap: 12px; padding: 11px 12px; border-radius: 9px; color: var(--muted); text-decoration: none; font-size: .88rem; font-weight: 600; transition: .18s ease; }
        .side-nav a:hover, .side-nav a.active { color: var(--green-dark); background: var(--mint); }
        .side-icon { width: 20px; text-align: center; font-size: 1rem; }
        .sidebar-bottom { margin-top: auto; padding: 18px 10px 0; border-top: 1px solid var(--line); }
        .user-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: .82rem; font-weight: 700; }
        .user-role { margin: 3px 0 14px; color: var(--muted); font-size: .72rem; text-transform: capitalize; }
        .logout-form { margin: 0; }
        .logout-form .btn { width: 100%; }
        .workspace { flex: 1; min-width: 0; }
        .topbar { min-height: 76px; padding: 16px clamp(20px, 4vw, 42px); display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--line); background: rgba(255,254,250,.72); }
        .topbar-title { color: var(--muted); font-size: .82rem; }
        .topbar-title strong { display: block; margin-top: 3px; color: var(--ink); font-size: 1rem; }
        .topbar-date { color: var(--muted); font-size: .8rem; }
        .login-header { background: var(--white); padding: 20px clamp(20px, 4vw, 42px); border-bottom: 1px solid var(--line); color: var(--green); font: 800 1.1rem 'Manrope', sans-serif; }
        .container { max-width: 1240px; margin: 30px auto; padding: 0 clamp(18px, 3vw, 38px); min-height: calc(100vh - 160px); }
        .card { background: var(--white); border-radius: 16px; padding: clamp(20px, 3vw, 34px); border: 1px solid var(--line); box-shadow: 0 12px 34px rgba(40, 61, 51, .055); }
        h2 { letter-spacing: -.045em; font-size: clamp(1.35rem, 2vw, 1.8rem); margin-top: 0; color: var(--ink); }
        h3 { letter-spacing: -.03em; color: var(--ink); }
        p { line-height: 1.65; }
        .grid-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 16px; margin-top: 24px; }
        .stat-box { background: #f8f8f2; padding: 21px; border-radius: 12px; text-align: center; border: 1px solid var(--line); }
        .stat-box span { font-size: .72rem; color: var(--muted); text-transform: uppercase; letter-spacing: .09em; font-weight: 700; }
        .stat-box strong { display: block; font-size: 2rem; color: var(--green); margin-top: 8px; }
        table { width: 100%; border-collapse: collapse; margin-top: 22px; font-size: .88rem; }
        th, td { padding: 14px 12px; text-align: left; border-bottom: 1px solid var(--line); }
        th { color: var(--muted); font-weight: 700; font-size: .72rem; text-transform: uppercase; letter-spacing: .07em; background: #f8f8f2; }
        tbody tr:hover { background: #fbfbf7; }
        .btn { display: inline-block; background: var(--green); color: white; padding: 10px 16px; border-radius: 8px; text-decoration: none; font-weight: 700; font-size: .84rem; border: 0; cursor: pointer; transition: background .18s ease, transform .18s ease; }
        .btn:hover { background: var(--green-dark); transform: translateY(-1px); }
        .btn-success { background: #397b61; }
        .btn-success:hover { background: #2d654f; }
        .btn-danger { background: var(--coral); }
        .btn-danger:hover { background: #a94f3d; }
        .form-group { margin-bottom: 18px; }
        label { display: block; font-size: .84rem; margin-bottom: 7px; color: #42564e; font-weight: 700; }
        input, select, textarea { width: 100%; padding: 11px 13px; border-radius: 8px; border: 1px solid #d8ddd4; background: #fff; color: var(--ink); font: inherit; box-sizing: border-box; outline: none; transition: border .18s ease, box-shadow .18s ease; }
        input:focus, select:focus, textarea:focus { border-color: #76a68f; box-shadow: 0 0 0 3px rgba(58, 123, 97, .12); }
        .alerta-erro { background: #fff0eb; border: 1px solid #f0c6b8; color: #8d3e2e; padding: 12px 14px; border-radius: 9px; margin-bottom: 17px; font-size: .88rem; }
        footer { text-align: center; padding: 22px 18px; color: var(--muted); font-size: .76rem; border-top: 1px solid var(--line); margin-top: 36px; }
        @media (max-width: 760px) { .sidebar { width: 76px; flex-basis: 76px; padding: 18px 9px; } .brand { padding: 4px 0 25px; text-align: center; font-size: .8rem; } .brand small, .nav-label, .side-text, .sidebar-bottom .user-name, .sidebar-bottom .user-role, .logout-label { display: none; } .side-nav a { justify-content: center; padding: 12px 4px; } .side-icon { font-size: 1.15rem; } .sidebar-bottom { padding: 14px 0 0; } .logout-form .btn { padding: 9px 4px; } .topbar { min-height: 66px; } }
        @media (max-width: 520px) { .sidebar { width: 62px; flex-basis: 62px; } .topbar-date { display: none; } .container { margin: 20px auto; padding: 0 12px; } .card { padding: 18px; overflow-x: auto; } }
    </style>
</head>
<body>
<?php if ($usuarioAutenticado): ?>
<div class="app-shell">
    <aside class="sidebar">
        <div class="brand">VetSystem<small>Gestão veterinária</small></div>
        <div class="nav-label">Menu principal</div>
        <nav class="side-nav">
            <a href="/" class="<?= $paginaAtual === '/' ? 'active' : '' ?>"><span class="side-icon">⌂</span><span class="side-text">Painel</span></a>
            <a href="/tutores" class="<?= str_starts_with($paginaAtual, '/tutores') ? 'active' : '' ?>"><span class="side-icon">♧</span><span class="side-text">Tutores</span></a>
            <a href="/pets" class="<?= str_starts_with($paginaAtual, '/pets') ? 'active' : '' ?>"><span class="side-icon">♧</span><span class="side-text">Pets</span></a>
            <a href="/agendamentos" class="<?= str_starts_with($paginaAtual, '/agendamentos') ? 'active' : '' ?>"><span class="side-icon">▦</span><span class="side-text">Agendamentos</span></a>
        </nav>
        <div class="sidebar-bottom">
            <div class="user-name"><?= htmlspecialchars($usuarioLogado) ?></div>
            <div class="user-role"><?= htmlspecialchars($_SESSION['usuario_perfil'] ?? 'Usuário') ?></div>
            <form action="/logout" method="POST" class="logout-form">
                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn btn-danger"><span class="logout-label">Sair da conta</span><span aria-hidden="true">↗</span></button>
            </form>
        </div>
    </aside>
    <div class="workspace">
        <header class="topbar">
            <div class="topbar-title">Área de trabalho<strong><?= htmlspecialchars($titulo ?? 'Painel') ?></strong></div>
            <div class="topbar-date">Sistema de gestão da clínica</div>
        </header>
        <main class="container">
<?php else: ?>
<div class="workspace">
    <header class="login-header">VetSystem <span style="font-weight:500;color:var(--muted);">/ Clínica veterinária</span></header>
    <main class="container">
<?php endif; ?>
