<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'lang.php';
?>

<style>
.navbar-top-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    width: 100%;
    max-width: 100%;
    padding: 12px 20px;
    background-color: #0d0f12;
    border-bottom: 1px solid #262626;
}

.navbar-left {
    display: flex;
    align-items: center;
    gap: 20px;
    flex: 0 0 auto;
}

.logo-section {
    display: flex;
    align-items: center;
    gap: 8px;
}

.navbar-center {
    display: flex;
    gap: 20px;
    align-items: center;
    flex: 1 1 auto;
    justify-content: center;
}

.time-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 0 15px;
    border-right: 1px solid #333;
}

.time-box:last-child {
    border-right: none;
}

.time-label {
    font-size: 11px;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 2px;
}

.time-value {
    font-size: 18px;
    font-weight: 700;
    color: #ffffff;
    font-family: 'Courier New', monospace;
    line-height: 1;
}

.time-date {
    font-size: 10px;
    color: #9ca3af;
    margin-top: 2px;
}

.navbar-right {
    display: flex;
    align-items: center;
    gap: 15px;
    flex: 0 0 auto;
}

.lang-toggle {
    display: flex;
    gap: 8px;
    align-items: center;
    padding-left: 15px;
    border-left: 1px solid #333;
}

.lang-btn {
    background: none;
    border: none;
    color: #d7d7d7;
    cursor: pointer;
    font-weight: 600;
    font-size: 12px;
    padding: 5px 8px;
    border-radius: 3px;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 4px;
    text-decoration: none;
}

.lang-btn.active {
    color: #ffffff;
    background-color: rgba(255, 255, 255, 0.1);
}

.lang-btn:hover {
    color: #ffffff;
}

.auth-buttons {
    display: flex;
    gap: 8px;
}

.btn-login {
    background: transparent;
    border: 1px solid #ffffff;
    color: #ffffff;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    white-space: nowrap;
}

.btn-login:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.btn-register {
    background: #22c55e;
    border: none;
    color: #000000;
    padding: 8px 14px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 600;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    white-space: nowrap;
}

.btn-register:hover {
    background-color: #16a34a;
    transform: translateY(-2px);
}

.user-menu {
    display: flex;
    align-items: center;
    gap: 10px;
}

.user-name {
    color: #ffffff;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.dropdown-user {
    position: relative;
    display: inline-block;
}

.dropdown-user:hover .dropdown-content {
    display: block;
}

.dropdown-content {
    display: none;
    position: absolute;
    right: 0;
    background-color: #1a1f3a;
    min-width: 180px;
    box-shadow: 0 8px 16px 0 rgba(0,0,0,0.3);
    padding: 8px 0;
    z-index: 1000;
    border: 1px solid #2a3f5f;
    border-radius: 5px;
    top: 100%;
}

.dropdown-content a {
    color: #d7d7d7;
    padding: 8px 12px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 13px;
    transition: all 0.2s ease;
}

.dropdown-content a:hover {
    background-color: #1c2025;
    color: #22c55e;
}

@media (max-width: 1200px) {
    .navbar-center {
        display: none;
    }
}
</style>

<div class="navbar-top-bar">
    <div class="navbar-left">
        <a href="index.php" style="text-decoration: none; display: flex; align-items: center; gap: 8px;">
            <div style="display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-tower-broadcast" style="font-size: 24px; color: #ff3b3b;"></i>
                <div style="line-height: 1;">
                    <div style="font-size: 16px; font-weight: 800; color: #ffd000;">446<span style="color: #ffffff;">Cluster</span></div>
                    <div style="font-size: 9px; color: #9ca3af; letter-spacing: 1px; margin-top: 2px;">PMR446 DX CLUSTER</div>
                </div>
            </div>
        </a>
    </div>

    <div class="navbar-center">
        <div class="time-box">
            <div class="time-label">UTC</div>
            <div class="time-value" id="utcTime">00:00:00</div>
            <div class="time-date" id="utcDate">00.00.0000</div>
        </div>
        <div class="time-box">
            <div class="time-label">LOCAL</div>
            <div class="time-value" id="localTime">00:00:00</div>
            <div class="time-date" id="localDate">00.00.0000</div>
        </div>
    </div>

    <div class="navbar-right">
        <?php if (isset($_SESSION['operator'])): ?>
            <div class="user-menu">
                <div class="user-name">
                    <i class="fa-solid fa-user-circle"></i>
                    <?= htmlspecialchars($_SESSION['operator']) ?>
                </div>
                <div class="dropdown-user">
                    <button style="background: none; border: none; color: #ffffff; cursor: pointer; font-size: 14px; padding: 4px 8px;">
                        <i class="fa-solid fa-chevron-down"></i>
                    </button>
                    <div class="dropdown-content">
                        <a href="profile.php">
                            <i class="fa-solid fa-id-card"></i>
                            <?= t('my_profile_link') ?>
                        </a>
                        <a href="spot_add.php">
                            <i class="fa-solid fa-plus"></i>
                            <?= t('add_spot_link') ?>
                        </a>
                        <a href="my_spots.php">
                            <i class="fa-solid fa-list"></i>
                            <?= t('my_spots_link') ?>
                        </a>
                        <a href="logout.php" style="border-top: 1px solid #2a3f5f; margin-top: 4px; padding-top: 8px;">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <?= t('logout') ?>
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="auth-buttons">
                <a href="login.php" class="btn-login">
                    <i class="fa-solid fa-right-to-bracket"></i>
                    Zaloguj się
                </a>
                <a href="register.php" class="btn-register">
                    <i class="fa-solid fa-check"></i>
                    Rejestracja
                </a>
            </div>
        <?php endif; ?>

        <div class="lang-toggle">
            <a href="?lang=pl" class="lang-btn <?= ($lang === 'pl') ? 'active' : '' ?>">
                <i class="fa-solid fa-flag"></i> Polski
            </a>
            <a href="?lang=en" class="lang-btn <?= ($lang === 'en') ? 'active' : '' ?>">
                <i class="fa-solid fa-flag"></i> English
            </a>
        </div>
    </div>
</div>

<script>
function updateTime() {
    const utcNow = new Date();
    const utcTime = utcNow.toLocaleTimeString('pl-PL', { hour12: false, timeZone: 'UTC' });
    const utcDate = utcNow.toLocaleDateString('pl-PL', { timeZone: 'UTC' });
    document.getElementById('utcTime').textContent = utcTime;
    document.getElementById('utcDate').textContent = utcDate;

    const localNow = new Date();
    const localTime = localNow.toLocaleTimeString('pl-PL', { hour12: false });
    const localDate = localNow.toLocaleDateString('pl-PL');
    document.getElementById('localTime').textContent = localTime;
    document.getElementById('localDate').textContent = localDate;
}

updateTime();
setInterval(updateTime, 1000);
</script>
