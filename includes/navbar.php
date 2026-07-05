<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav class="navbar navbar-expand-lg navbar-dark main-navbar">

    <div class="container-fluid">

        <a class="navbar-brand logo" href="index.php">

            <span class="logo446">446</span><span class="logoCluster">Cluster</span>

            <div class="logoSub">
                PMR DX Cluster
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

                <li class="nav-item">
                    <a class="nav-link active" href="index.php">
                        <i class="fa-solid fa-satellite-dish"></i>
                        Spoty LIVE
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">
                        <i class="fa-solid fa-calendar-days"></i>
                        Planowane
                    </a>
                </li>

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
                                <a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-id-card"></i>
                                    Mój profil
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="spot_add.php">
                                    <i class="fa-solid fa-plus"></i>
                                    Dodaj spot
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="#">
                                    <i class="fa-solid fa-list"></i>
                                    Moje spoty
                                </a>
                            </li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <a class="dropdown-item" href="logout.php">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    Wyloguj
                                </a>
                            </li>

                        </ul>

                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="login.php">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            Logowanie
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="register.php">
                            <i class="fa-solid fa-user-plus"></i>
                            Rejestracja
                        </a>
                    </li>

                <?php endif; ?>

            </ul>

        </div>

    </div>

</nav>