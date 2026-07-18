<?php
// NAJPIERW ustaw język Z URL
if (!empty($_GET['lang'])) {
    $_SESSION['lang'] = strtoupper($_GET['lang']);
}

include 'includes/db.php';
include 'includes/lang.php';

if (isset($_SESSION['operator'])) {
    header('Location: index.php');
    exit;
}

$error = '';
$current_lang = $_SESSION['lang'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operator = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($operator) || empty($password)) {
        $error = t('fill_all_fields');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM operators WHERE operator = ?");
        $stmt->execute([$operator]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['operator'] = $operator;
            $_SESSION['lang'] = $current_lang;
            header('Location: index.php');
            exit;
        } else {
            $error = t('invalid_login');
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.login-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 20px;
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.login-container {
    max-width: 450px;
    width: 100%;
    background-color: transparent;
    border: 2px solid #ff3b3b;
    border-radius: 8px;
    padding: 40px;
}

.login-header {
    text-align: center;
    margin-bottom: 30px;
}

.login-logo {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.login-title {
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 10px;
}

.login-subtitle {
    font-size: 0.95rem;
    color: #9ca3af;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-weight: 600;
    background-color: rgba(239, 68, 68, 0.1);
    border: 1px solid #ef4444;
    color: #ef4444;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    margin-bottom: 8px;
    color: #e5e7eb;
    font-weight: 600;
    font-size: 0.95rem;
}

.form-input {
    width: 100%;
    padding: 12px 15px;
    background-color: #1a1f3a;
    border: 1px solid #ff3b3b;
    color: #e5e7eb;
    border-radius: 4px;
    font-size: 1rem;
    transition: all 0.3s ease;
    box-sizing: border-box;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.form-input:focus {
    outline: none;
    border-color: #ff3b3b;
    box-shadow: 0 0 10px rgba(255, 59, 59, 0.3);
    background-color: #1a1f3a;
}

.btn-login {
    width: 100%;
    padding: 12px;
    background-color: #ef4444;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-login:hover {
    background-color: #dc2626;
    box-shadow: 0 8px 16px rgba(239, 68, 68, 0.3);
}

.login-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 59, 59, 0.2);
}

.login-footer-text {
    color: #9ca3af;
    font-size: 0.95rem;
    margin-bottom: 10px;
}

.btn-register {
    display: inline-block;
    padding: 8px 16px;
    background-color: #10b981;
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-register:hover {
    background-color: #059669;
    box-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
}

@media (max-width: 768px) {
    .login-wrapper {
        padding: 15px;
    }

    .login-container {
        padding: 25px;
    }

    .login-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="login-wrapper">
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">📡</div>
            <div class="login-title"><?= t('login_title') ?></div>
            <div class="login-subtitle"><?= t('login_subtitle') ?></div>
        </div>

        <?php if ($error): ?>
            <div class="alert">
                <i class="fa-solid fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label class="form-label" for="username">
                    <i class="fa-solid fa-user"></i> <?= t('username_label') ?>
                </label>
                <input type="text" id="username" name="username" class="form-input" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label" for="password">
                    <i class="fa-solid fa-lock"></i> <?= t('password_label') ?>
                </label>
                <input type="password" id="password" name="password" class="form-input" required>
            </div>

            <button type="submit" class="btn-login">
                <i class="fa-solid fa-sign-in-alt"></i> <?= t('login_button') ?>
            </button>
        </form>

        <div class="login-footer">
            <div class="login-footer-text"><?= t('no_account') ?></div>
            <a href="register.php?lang=<?= htmlspecialchars($current_lang) ?>" class="btn-register">
                <i class="fa-solid fa-user-plus"></i> <?= t('register_button') ?>
            </a>
        </div>
    </div>
</div>

<script>
window.addEventListener('load', function() {
    document.getElementById('password').value = '';
});

setTimeout(() => {
    document.getElementById('password').value = '';
}, 100);
</script>

<?php include 'includes/footer.php'; ?>