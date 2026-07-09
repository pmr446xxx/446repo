<?php
require_once 'includes/db.php';

$page = $_GET['page'] ?? 'home';
$page = preg_replace('/[^a-z0-9_-]/', '', $page);

$valid_pages = ['home', 'login', 'register', 'dashboard', 'spots', 'map'];

if (!in_array($page, $valid_pages, true)) {
    $page = 'home';
}

?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>446CLUSTER - PMR Network</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }

        header {
            background: rgba(0, 0, 0, 0.8);
            border-bottom: 2px solid #00d4ff;
            padding: 20px 0;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        header h1 {
            color: #00d4ff;
            font-size: 28px;
            text-shadow: 0 0 10px rgba(0, 212, 255, 0.5);
        }

        nav {
            display: flex;
            gap: 20px;
        }

        nav a {
            color: #e0e0e0;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
            transition: all 0.3s;
        }

        nav a:hover {
            background: #00d4ff;
            color: #000;
        }

        .main {
            padding: 40px 0;
        }

        .card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #00d4ff;
            border-radius: 8px;
            padding: 30px;
            margin: 20px 0;
            backdrop-filter: blur(10px);
        }

        .card h2 {
            color: #00d4ff;
            margin-bottom: 20px;
        }

        .button {
            background: #00d4ff;
            color: #000;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
            transition: all 0.3s;
        }

        .button:hover {
            background: #00a8cc;
            transform: scale(1.05);
        }

        input, textarea {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #00d4ff;
            background: rgba(255, 255, 255, 0.1);
            color: #e0e0e0;
            border-radius: 4px;
            font-family: inherit;
        }

        input:focus, textarea:focus {
            outline: none;
            background: rgba(0, 212, 255, 0.1);
            border-color: #00a8cc;
        }

        footer {
            background: rgba(0, 0, 0, 0.9);
            border-top: 2px solid #00d4ff;
            padding: 20px 0;
            text-align: center;
            margin-top: 60px;
            color: #888;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>🔊 446CLUSTER</h1>
            <nav>
                <a href="index-new.php?page=home">Strona główna</a>
                <a href="index-new.php?page=login">Logowanie</a>
                <a href="index-new.php?page=register">Rejestracja</a>
                <a href="index-new.php?page=dashboard">Dashboard</a>
                <a href="index-new.php?page=map">Mapa</a>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?php
            if ($page === 'home') {
                echo '<div class="card">';
                echo '<h2>Witaj na 446CLUSTER!</h2>';
                echo '<p>Platforma komunikacji PMR dla Polskich operatorów.</p>';
                echo '<p style="margin-top: 20px;"><a href="index-new.php?page=login" class="button">Zaloguj się</a></p>';
                echo '</div>';
            } elseif ($page === 'login') {
                echo '<div class="card">';
                echo '<h2>Logowanie</h2>';
                echo '<form method="POST" action="auth/login.php">';
                echo '<input type="text" name="operator" placeholder="Operator/Call Sign" required>';
                echo '<input type="password" name="password" placeholder="Hasło" required>';
                echo '<button type="submit" class="button">Zaloguj</button>';
                echo '</form>';
                echo '</div>';
            } elseif ($page === 'register') {
                echo '<div class="card">';
                echo '<h2>Rejestracja</h2>';
                echo '<form method="POST" action="auth/register.php">';
                echo '<input type="text" name="operator" placeholder="Operator/Call Sign" required>';
                echo '<input type="email" name="email" placeholder="Email" required>';
                echo '<input type="password" name="password" placeholder="Hasło" required>';
                echo '<input type="password" name="password_confirm" placeholder="Potwierdź hasło" required>';
                echo '<input type="text" name="country" placeholder="Kraj (np. PL)" maxlength="2">';
                echo '<button type="submit" class="button">Zarejestruj</button>';
                echo '</form>';
                echo '</div>';
            } elseif ($page === 'dashboard') {
                echo '<div class="card">';
                echo '<h2>Dashboard</h2>';
                echo '<p>Zaloguj się aby zobaczyć dashboard.</p>';
                echo '</div>';
            } elseif ($page === 'spots') {
                echo '<div class="card">';
                echo '<h2>Ostatnie Spoty</h2>';
                echo '<p>Brak spotów do wyświetlenia.</p>';
                echo '</div>';
            } elseif ($page === 'map') {
                echo '<div class="card">';
                echo '<h2>Mapa Operatorów</h2>';
                echo '<p>Mapa będzie tutaj wkrótce.</p>';
                echo '</div>';
            }
            ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 446CLUSTER - PMR Network | Polska</p>
        </div>
    </footer>
</body>
</html>