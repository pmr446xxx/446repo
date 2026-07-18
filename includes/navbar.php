<?php
$current_lang = $_SESSION['lang'] ?? 'PL';
?>
<nav class="navbar navbar-expand-lg navbar-dark custom-navbar">
    <div class="container-fluid custom-container">
        <a class="navbar-brand custom-brand" href="index.php">
            <div class="logo-wrapper">
                <div class="logo-446">446</div>
                <div class="logo-spacing"></div>
                <div class="logo-dx-wrapper">
                    <div class="logo-dx">DX</div>
                </div>
                <div class="logo-pl">.pl</div>
            </div>
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav align-items-center" style="margin-left: auto;">
                <li class="nav-item contact-item">
                    <a class="nav-link contact-link" href="mailto:admin@446dx.pl" title="Kontakt">
                        <i class="fa-solid fa-envelope"></i>
                    </a>
                    <span class="contact-tooltip">admin@446dx.pl</span>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link" href="index.php">
                        <i class="fa-solid fa-home"></i> <span><?= t('home') ?></span>
                    </a>
                </li>

                <?php if (isset($_SESSION['operator'])): ?>
                    <li class="nav-item">
                        <a class="nav-link login-display" href="javascript:void(0);" onclick="toggleDropdown(event)">
                            <i class="fa-solid fa-user-circle"></i> <span class="login-name"><?= htmlspecialchars($_SESSION['operator']) ?></span> <i class="fa-solid fa-chevron-down" style="font-size: 0.7rem;"></i>
                        </a>
                        <div class="dropdown-menu-custom" id="userDropdown">
                            <a href="spot_add.php" class="dropdown-item-custom"><i class="fa-solid fa-plus"></i> <?= t('add_spot') ?></a>
                            <a href="index.php?page=my_spots" class="dropdown-item-custom"><i class="fa-solid fa-list"></i> <?= t('my_spots') ?></a>
                            <a href="index.php?page=my_profile" class="dropdown-item-custom"><i class="fa-solid fa-user"></i> <?= t('my_profile') ?></a>
                            <?php if ($_SESSION['operator'] === 'admin'): ?>
                                <hr style="margin: 5px 0; border-color: rgba(255, 59, 59, 0.2);">
                                <a href="admin.php" class="dropdown-item-custom admin-item"><i class="fa-solid fa-shield"></i> Admin</a>
                            <?php endif; ?>
                            <hr style="margin: 5px 0; border-color: rgba(255, 59, 59, 0.2);">
                            <a href="logout.php" class="dropdown-item-custom logout-item"><i class="fa-solid fa-sign-out-alt"></i> <?= t('logout') ?></a>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php?lang=<?= htmlspecialchars($current_lang) ?>">
                            <i class="fa-solid fa-sign-in-alt"></i> <span><?= t('login') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php?lang=<?= htmlspecialchars($current_lang) ?>">
                            <i class="fa-solid fa-user-plus"></i> <span><?= t('register') ?></span>
                        </a>
                    </li>
                <?php endif; ?>

                <li class="nav-item separator"></li>

                <li class="nav-item flag-container">
                    <a href="?lang=PL" class="flag-link <?= $current_lang === 'PL' ? 'active' : '' ?>" title="Polski">
                        <svg class="flag-animated flag-pulse" width="28" height="18" viewBox="0 0 30 20"><rect width="30" height="10" fill="#FFFFFF"/><rect y="10" width="30" height="10" fill="#DC143C"/></svg>
                    </a>
                    <a href="?lang=EN" class="flag-link <?= $current_lang === 'EN' ? 'active' : '' ?>" title="English">
                        <svg class="flag-animated flag-pulse" width="28" height="18" viewBox="0 0 60 40"><rect width="60" height="40" fill="#012169"/><path d="M0,0 L60,40 M60,0 L0,40" stroke="#FFF" stroke-width="6"/><path d="M0,0 L60,40 M60,0 L0,40" stroke="#C8102E" stroke-width="4"/><path d="M30,0 V40 M0,20 H60" stroke="#FFF" stroke-width="10"/><path d="M30,0 V40 M0,20 H60" stroke="#C8102E" stroke-width="6"/></svg>
                    </a>
                    <a href="?lang=DE" class="flag-link <?= $current_lang === 'DE' ? 'active' : '' ?>" title="Deutsch">
                        <svg class="flag-animated flag-pulse" width="28" height="18" viewBox="0 0 30 20"><rect width="30" height="6.67" fill="#000000"/><rect y="6.67" width="30" height="6.67" fill="#DD0000"/><rect y="13.33" width="30" height="6.67" fill="#FFCE00"/></svg>
                    </a>
                    <a href="?lang=FR" class="flag-link <?= $current_lang === 'FR' ? 'active' : '' ?>" title="Français">
                        <svg class="flag-animated flag-pulse" width="28" height="18" viewBox="0 0 30 20"><rect width="10" height="20" fill="#002395"/><rect x="10" width="10" height="20" fill="#FFFFFF"/><rect x="20" width="10" height="20" fill="#ED2939"/></svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<style>
    @keyframes pulse-dx {
        0%, 100% { 
            font-size: 2.8rem;
            transform: scale(1);
        }
        50% { 
            font-size: 2.2rem;
            transform: scale(0.8);
        }
    }

    @keyframes envelope-pulse {
        0%, 100% { transform: scale(1); opacity: 1; }
        50% { transform: scale(1.1); opacity: 0.9; }
    }

    @keyframes flagPulse {
        0%, 100% { opacity: 1; filter: drop-shadow(0 0 0px rgba(255, 255, 255, 0)); }
        50% { opacity: 0.7; filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.5)); }
    }
    
    .custom-navbar {
        background-color: #000000 !important;
        border-bottom: 2px solid #ff3b3b;
        padding: 12px 0 !important;
        margin: 0 !important;
    }
    
    .custom-container {
        max-width: 100% !important;
        padding: 0 20px !important;
        margin: 0 !important;
        display: flex !important;
        align-items: center !important;
        background-color: #000000 !important;
        justify-content: center !important;
    }
    
    .custom-brand {
        display: flex;
        align-items: center;
        gap: 0;
        font-weight: bold;
        margin: 0 !important;
        padding: 0 !important;
        flex-shrink: 0;
        text-decoration: none;
        background-color: #000000 !important;
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
    }

    .logo-wrapper {
        display: flex;
        align-items: center;
        gap: 0;
        width: max-content;
        height: 40px;
        background-color: #000000 !important;
    }

    .logo-446 {
        font-size: 2rem;
        color: #fbbf24;
        letter-spacing: 2px;
        font-weight: 900;
        line-height: 1;
        height: 40px;
        display: flex;
        align-items: center;
        white-space: nowrap;
        background-color: #000000 !important;
    }

    .logo-spacing {
        width: 8px;
        background-color: #000000 !important;
    }

    .logo-dx-wrapper {
        width: 50px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #000000 !important;
    }

    .logo-dx {
        font-size: 2.8rem;
        color: #ef4444;
        animation: pulse-dx 1.5s ease-in-out infinite;
        line-height: 1;
        font-weight: 900;
        letter-spacing: -3px;
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #000000 !important;
    }

    .logo-pl {
        font-size: 1rem;
        color: #10b981;
        font-weight: 700;
        line-height: 1;
        margin-left: 2px;
        height: 40px;
        display: flex;
        align-items: center;
        white-space: nowrap;
        background-color: #000000 !important;
    }
    
    .navbar-collapse {
        flex-grow: 1 !important;
        background-color: #000000 !important;
    }
    
    .navbar-nav {
        display: flex !important;
        align-items: center !important;
        flex-wrap: nowrap;
        gap: 8px;
        margin: 0 !important;
        padding: 0 !important;
        margin-left: auto !important;
        width: auto;
        background-color: #000000 !important;
    }
    
    .nav-item {
        margin: 0 !important;
        padding: 0 !important;
        white-space: nowrap;
        position: relative;
        background-color: #000000 !important;
    }
    
    .nav-link {
        color: #fff !important;
        padding: 8px 12px !important;
        margin: 0 !important;
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        background-color: #000000 !important;
    }
    
    .nav-link span {
        display: inline-block;
        background-color: #000000 !important;
    }
    
    .nav-link:hover {
        color: #ff3b3b !important;
    }

    .contact-item {
        position: relative;
        background-color: #000000 !important;
    }

    .contact-link {
        color: #ffff00 !important;
        font-size: 1.1rem !important;
        animation: envelope-pulse 2s ease-in-out infinite;
        padding: 8px 10px !important;
        cursor: pointer;
        background-color: #000000 !important;
    }

    .contact-link:hover {
        animation: none;
        transform: scale(1.2);
        color: #ffeb3b !important;
    }

    .contact-tooltip {
        position: absolute;
        left: -110px;
        top: 50%;
        transform: translateY(-50%);
        color: #ffffff;
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease;
        z-index: 100;
        background-color: #000000 !important;
    }

    .contact-item:hover .contact-tooltip {
        opacity: 1;
    }
    
    .login-display {
        color: #ffff00 !important;
        font-weight: 700 !important;
        cursor: pointer;
        background-color: #000000 !important;
    }
    
    .login-name {
        font-family: monospace;
        background-color: #000000 !important;
    }
    
    .dropdown-menu-custom {
        display: none;
        position: absolute;
        top: 100%;
        right: 0;
        background-color: #000000;
        border: 1px solid #ff3b3b;
        border-radius: 4px;
        min-width: 200px;
        z-index: 1000;
        margin-top: 8px;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    }
    
    .dropdown-menu-custom.show {
        display: block;
    }
    
    .dropdown-item-custom {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 15px;
        color: #fff;
        text-decoration: none;
        transition: all 0.2s ease;
        background-color: #000000;
    }
    
    .dropdown-item-custom:hover {
        background-color: rgba(255, 59, 59, 0.1);
        color: #ff3b3b;
    }
    
    .dropdown-item-custom.admin-item {
        color: #ff3b3b;
        background-color: #000000;
    }
    
    .dropdown-item-custom.logout-item {
        color: #ef4444;
        background-color: #000000;
    }
    
    .separator {
        width: 1px;
        height: 20px;
        background-color: rgba(255, 59, 59, 0.3);
        margin: 0 8px !important;
    }
    
    .flag-container {
        display: flex;
        gap: 8px;
        align-items: center;
        margin-left: 8px !important;
        background-color: #000000 !important;
    }
    
    .flag-link {
        display: inline-block;
        cursor: pointer;
        transition: all 0.3s ease;
        opacity: 0.65;
        text-decoration: none;
        border-radius: 3px;
        padding: 2px;
        background-color: #000000 !important;
    }
    
    .flag-link:hover {
        opacity: 1;
        transform: scale(1.15);
    }
    
    .flag-link.active {
        opacity: 1;
        transform: scale(1.2);
        box-shadow: 0 0 10px #ff3b3b;
        border-radius: 4px;
    }

    .flag-animated {
        border-radius: 2px;
        border: 1px solid rgba(255, 255, 255, 0.3);
        display: block;
        animation: flagPulse 2s ease-in-out infinite;
        background-color: #000000 !important;
    }

    @media (max-width: 768px) {
        .logo-446 {
            font-size: 1.4rem;
        }

        .logo-dx {
            font-size: 2rem;
        }

        .logo-pl {
            font-size: 0.8rem;
        }

        .logo-dx-wrapper {
            width: 40px;
        }
    }
</style>

<script>
function toggleDropdown(e) {
    e.preventDefault();
    const dropdown = document.getElementById('userDropdown');
    if (dropdown) {
        dropdown.classList.toggle('show');
    }
}

document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('userDropdown');
    if (dropdown && !e.target.closest('.nav-item')) {
        dropdown.classList.remove('show');
    }
});
</script>