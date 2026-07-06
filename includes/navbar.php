<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'lang.php';
?>

<style>
.logo-section {
    display: flex;
    align-items: center;
    gap: 12px;
}

.heart-animation {
    font-size: 24px;
    animation: heartbeat 1.2s infinite;
    color: #ff3b3b;
}

@keyframes heartbeat {
    0%, 100% {
        transform: scale(1);
        opacity: 1;
    }
    25% {
        transform: scale(1.1);
        opacity: 1;
    }
    50% {
        transform: scale(1);
        opacity: 0.9;
    }
    75% {
        transform: scale(1.15);
        opacity: 1;
    }
}

.dx-link {
    font-size: 18px;
    font-weight: 800;
    color: #fbbf24;
    text-decoration: none;
    letter-spacing: 0.5px;
    transition: all 0.3s ease;
}

.dx-link:hover {
    color: #fcd34d;
    text-shadow: 0 0 10px rgba(251, 191, 36, 0.5);
}
</style>

<nav class="navbar navbar-expand-lg navbar-dark main-navbar">

    <div class="container-fluid">

        <a class="navbar-brand logo" href="index.php">

            <div class="logo-section">
                <div>
                    <span class="logo446">446</span><span class="logoCluster">Cluster</span>
                    <div class="logoSub">
                        PMR DX Cluster
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px; margin-left: 20px; padding-left: 20px; border-left: 2px solid #333;">
                    <span class="heart-animation">❤️</span>
                    <a href="https://446dx.pl" target="_blank" class="dx-link">446DX.PL</a>
                </div>
            </div>

        </a>

        <button class="navbar-toggler"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainMenu">

            <span class="navbar-toggler-icon"></span>

        </button>

        <div class="collapse navbar-collapse" id="mainMenu">

            <ul class="navbar-nav ms-auto">

                <?php if (isset($_SESSION['operator'])): ?>

                    <li class="nav-item dropdown">

                        <a class="nav-link dropdown-toggle"
                           href="#"
                           role="button"
                           data-bs-toggle="dropdown">

                            <i class="fa-solid fa-user"></i>
                            <?= htmlspecialchars($_SESSION['operator']) ?>

                        </a>

                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">

                            <li>
                                <a class="dropdown-item" href="profile.php">
                                    <i class="fa-solid fa-id-card"></i>
                                    <?= t('my_profile_link') ?>
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="spot_add.php">
                                    <i class="fa-solid fa-plus"></i>
                                    <?= t('add_spot_link') ?>
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="my_spots.php">
                                    <i class="fa-solid fa-list"></i>
                                    <?= t('my_spots_link') ?>
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    <?= t('logout') ?>
                                </a>
                            </li>

                        </ul>

                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            <?= t('login') ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="fa-solid fa-user-plus"></i>
                            <?= t('register') ?>
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>

    </div>

</nav>
