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
$success = '';
$current_lang = $_SESSION['lang'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $password_confirm = trim($_POST['password_confirm'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $accept_terms = isset($_POST['accept_terms']) ? 1 : 0;

    if (empty($username) || empty($password) || empty($email)) {
        $error = t('fill_all_fields');
    } elseif (!$accept_terms) {
        $error = t('accept_terms');
    } elseif (strlen($username) < 3) {
        $error = t('username_short');
    } elseif (strlen($password) < 8) {
        $error = t('password_short');
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = t('password_uppercase');
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = t('password_lowercase');
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = t('password_digit');
    } elseif ($password !== $password_confirm) {
        $error = t('password_mismatch');
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = t('invalid_email');
    } else {
        $stmt = $pdo->prepare("SELECT id FROM operators WHERE username = ?");
        $stmt->execute([$username]);
        
        if ($stmt->fetch()) {
            $error = t('username_exists');
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("
                INSERT INTO operators (username, password, email, created_at)
                VALUES (?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([$username, $hashedPassword, $email])) {
                $success = t('register_success');
            } else {
                $error = t('register_error');
            }
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<style>
.register-wrapper {
    width: 100%;
    background-color: #0f1419;
    padding: 20px;
    margin: 0;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

.register-container {
    max-width: 450px;
    width: 100%;
    background-color: transparent;
    border: 2px solid #ff3b3b;
    border-radius: 8px;
    padding: 40px;
}

.register-header {
    text-align: center;
    margin-bottom: 30px;
}

.register-logo {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.register-title {
    font-size: 1.8rem;
    font-weight: 900;
    color: #fff;
    margin-bottom: 10px;
}

.register-subtitle {
    font-size: 0.95rem;
    color: #9ca3af;
}

.alert {
    padding: 15px;
    border-radius: 4px;
    margin-bottom: 20px;
    font-weight: 600;
}

.alert-error {
    background-color: rgba(239, 68, 68, 0.1);
    border: 1px solid #ef4444;
    color: #ef4444;
}

.alert-success {
    background-color: rgba(16, 185, 129, 0.1);
    border: 1px solid #10b981;
    color: #10b981;
}

.password-requirements {
    background-color: rgba(255, 59, 59, 0.05);
    border: 1px solid #ff3b3b;
    border-radius: 4px;
    padding: 12px;
    margin-bottom: 20px;
    font-size: 0.85rem;
    color: #9ca3af;
}

.requirement {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.requirement:last-child {
    margin-bottom: 0;
}

.requirement-icon {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    flex-shrink: 0;
    font-weight: bold;
}

.requirement-icon.incomplete {
    background-color: rgba(255, 59, 59, 0.3);
    color: #ff3b3b;
}

.requirement-icon.complete {
    background-color: rgba(16, 185, 129, 0.3);
    color: #10b981;
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

.terms-group {
    margin-bottom: 20px;
    padding: 15px;
    background-color: rgba(255, 59, 59, 0.05);
    border: 1px solid #ff3b3b;
    border-radius: 4px;
}

.terms-checkbox {
    display: flex;
    align-items: flex-start;
    gap: 12px;
}

.checkbox-input {
    width: 20px;
    height: 20px;
    margin-top: 2px;
    cursor: pointer;
    accent-color: #10b981;
    flex-shrink: 0;
}

.terms-label {
    flex: 1;
    color: #e5e7eb;
    font-size: 0.9rem;
    cursor: pointer;
    line-height: 1.5;
}

.terms-link {
    color: #10b981;
    text-decoration: none;
    font-weight: 600;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
    cursor: pointer;
}

.terms-link:hover {
    color: #059669;
    border-bottom-color: #10b981;
}

.btn-register {
    width: 100%;
    padding: 12px;
    background-color: #10b981;
    color: white;
    border: none;
    border-radius: 4px;
    font-size: 1rem;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-top: 10px;
}

.btn-register:hover {
    background-color: #059669;
    box-shadow: 0 8px 16px rgba(16, 185, 129, 0.3);
}

.register-footer {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid rgba(255, 59, 59, 0.2);
}

.register-footer-text {
    color: #9ca3af;
    font-size: 0.95rem;
    margin-bottom: 10px;
}

.btn-login {
    display: inline-block;
    padding: 8px 16px;
    background-color: #ef4444;
    color: white;
    border: none;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-login:hover {
    background-color: #dc2626;
    box-shadow: 0 4px 8px rgba(239, 68, 68, 0.3);
}

@media (max-width: 768px) {
    .register-wrapper {
        padding: 15px;
    }

    .register-container {
        padding: 25px;
    }

    .register-title {
        font-size: 1.5rem;
    }
}
</style>

<div class="register-wrapper">
    <div class="register-container">
        <div class="register-header">
            <div class="register-logo">📡</div>
            <div class="register-title"><?= t('register_title') ?></div>
            <div class="register-subtitle"><?= t('register_subtitle') ?></div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error">
                <i class="fa-solid fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                <br><br>
                <a href="login.php?lang=<?= htmlspecialchars($current_lang) ?>" class="btn-login" style="display: inline-block; margin-top: 10px;">
                    <?= t('go_to_login') ?>
                </a>
            </div>
        <?php else: ?>
            <form method="POST" onsubmit="clearPasswords()">
                <div class="form-group">
                    <label class="form-label" for="username">
                        <i class="fa-solid fa-user"></i> <?= t('username_label') ?>
                    </label>
                    <input type="text" id="username" name="username" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="email">
                        <i class="fa-solid fa-envelope"></i> <?= t('email_label') ?>
                    </label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password">
                        <i class="fa-solid fa-lock"></i> <?= t('password_label') ?>
                    </label>
                    <input type="password" id="password" name="password" class="form-input" required onkeyup="checkPassword()" onfocus="clearField(this)">
                    
                    <div class="password-requirements">
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-length">✗</span>
                            <span><?= t('min_8_chars') ?></span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-upper">✗</span>
                            <span><?= t('capital_letter') ?></span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-lower">✗</span>
                            <span><?= t('lowercase_letter') ?></span>
                        </div>
                        <div class="requirement">
                            <span class="requirement-icon incomplete" id="icon-digit">✗</span>
                            <span><?= t('digit_number') ?></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="password_confirm">
                        <i class="fa-solid fa-lock-open"></i> <?= t('password_confirm_label') ?>
                    </label>
                    <input type="password" id="password_confirm" name="password_confirm" class="form-input" required onfocus="clearField(this)">
                </div>

                <div class="terms-group">
                    <div class="terms-checkbox">
                        <input type="checkbox" id="accept_terms" name="accept_terms" class="checkbox-input" value="1" required>
                        <label for="accept_terms" class="terms-label">
                            <?= t('terms_text') ?> <a href="terms-of-service.php?lang=<?= htmlspecialchars($current_lang) ?>" target="_blank" class="terms-link"><?= t('terms_link') ?></a> 
                            i <a href="privacy-policy.php?lang=<?= htmlspecialchars($current_lang) ?>" target="_blank" class="terms-link"><?= t('privacy_link') ?></a>
                        </label>
                    </div>
                </div>

                <button type="submit" class="btn-register">
                    <i class="fa-solid fa-user-plus"></i> <?= t('register_button') ?>
                </button>
            </form>

            <div class="register-footer">
                <div class="register-footer-text"><?= t('have_account') ?></div>
                <a href="login.php?lang=<?= htmlspecialchars($current_lang) ?>" class="btn-login">
                    <i class="fa-solid fa-sign-in-alt"></i> <?= t('login_button') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
function clearField(field) {
    setTimeout(() => {
        if (field.value.length === 0 || /^•+$/.test(field.value)) {
            field.value = '';
        }
    }, 0);
}

function clearPasswords() {
    document.getElementById('password').value = '';
    document.getElementById('password_confirm').value = '';
}

function checkPassword() {
    const password = document.getElementById('password').value;
    
    const hasLength = password.length >= 8;
    const hasUpper = /[A-Z]/.test(password);
    const hasLower = /[a-z]/.test(password);
    const hasDigit = /[0-9]/.test(password);
    
    updateRequirement('length', hasLength);
    updateRequirement('upper', hasUpper);
    updateRequirement('lower', hasLower);
    updateRequirement('digit', hasDigit);
}

function updateRequirement(type, isComplete) {
    const icon = document.getElementById('icon-' + type);
    if (isComplete) {
        icon.classList.remove('incomplete');
        icon.classList.add('complete');
        icon.textContent = '✓';
    } else {
        icon.classList.remove('complete');
        icon.classList.add('incomplete');
        icon.textContent = '✗';
    }
}

window.addEventListener('load', function() {
    document.getElementById('password').value = '';
    document.getElementById('password_confirm').value = '';
});

setTimeout(() => {
    document.getElementById('password').value = '';
    document.getElementById('password_confirm').value = '';
}, 100);
</script>

<?php include 'includes/footer.php'; ?>