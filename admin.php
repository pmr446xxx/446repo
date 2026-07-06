<?php
session_start();

require_once 'includes/db.php';

// Sprawdzenie czy user jest adminem
if (!isset($_SESSION['operator']) || $_SESSION['operator'] !== 'admin') {
    header('Location: login.php');
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

$action = $_GET['action'] ?? '';
$message = '';
$error = '';

// Usuwanie spota
if ($action === 'delete' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        $stmt = $pdo->prepare("DELETE FROM spots WHERE id = ?");
        $stmt->execute([$id]);
        $message = 'Spot usunięty!';
    } catch (Exception $e) {
        $error = 'Błąd przy usuwaniu: ' . $e->getMessage();
    }
}

// Usuwanie operatora
if ($action === 'delete_operator' && isset($_GET['operator'])) {
    $operator = $_GET['operator'];
    try {
        $stmt = $pdo->prepare("DELETE FROM operators WHERE operator = ?");
        $stmt->execute([$operator]);
        $message = 'Operator usunięty!';
    } catch (Exception $e) {
        $error = 'Błąd przy usuwaniu: ' . $e->getMessage();
    }
}

?>

<div class="container-fluid mt-4">

    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="panel">
                <div class="panel-title">
                    <i class="fa-solid fa-shield"></i>
                    Panel Administracyjny
                </div>
            </div>
        </div>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle"></i>
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger">
            <i class="fa-solid fa-exclamation-circle"></i>
            <?= htmlspecialchars($error) ?>
        </div>
    <?php endif; ?>

    <!-- STATYSTYKI -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-satellite-dish"></i> Razem spotów
                </div>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM spots");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                ?>
                <div class="stats-value"><?= $count ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-users"></i> Razem operatorów
                </div>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM operators WHERE operator != 'admin'");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                ?>
                <div class="stats-value"><?= $count ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-database"></i> Dzisiaj spotów
                </div>
                <?php
                $stmt = $pdo->query("SELECT COUNT(*) as count FROM spots WHERE DATE(created_at) = CURDATE()");
                $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                ?>
                <div class="stats-value"><?= $count ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-road"></i> Km razem
                </div>
                <?php
                $stmt = $pdo->query("SELECT SUM(distance_km) as total FROM spots");
                $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                ?>
                <div class="stats-value"><?= round($total, 0) ?></div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- SPOTY -->
        <div class="col-lg-8">
            <div class="panel">
                <div class="panel-title">
                    <i class="fa-solid fa-satellite-dish"></i>
                    Ostatnie spoty
                </div>

                <div class="panel-body">
                    <table class="table table-dark table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Operator</th>
                                <th>Korespondent</th>
                                <th>CH</th>
                                <th>Lokacja</th>
                                <th>KM</th>
                                <th>Data</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("
                                SELECT * FROM spots 
                                ORDER BY id DESC 
                                LIMIT 50
                            ");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <tr>
                                    <td><?= $row['id'] ?></td>
                                    <td><strong><?= htmlspecialchars($row['operator']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['correspondent']) ?></td>
                                    <td>CH <?= $row['channel'] ?></td>
                                    <td><?= htmlspecialchars($row['location_from']) ?> → <?= htmlspecialchars($row['location_to']) ?></td>
                                    <td><?= $row['distance_km'] ?> km</td>
                                    <td><?= date('d.m.Y H:i', strtotime($row['created_at'])) ?></td>
                                    <td>
                                        <a href="?action=delete&id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Usunąć?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- OPERATORZY -->
        <div class="col-lg-4">
            <div class="panel">
                <div class="panel-title">
                    <i class="fa-solid fa-users"></i>
                    Operatorzy
                </div>

                <div class="panel-body">
                    <table class="table table-dark table-sm">
                        <thead>
                            <tr>
                                <th>Operator</th>
                                <th>Email</th>
                                <th>Akcje</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->query("
                                SELECT * FROM operators 
                                WHERE operator != 'admin'
                                ORDER BY created_at DESC
                            ");
                            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                            ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['operator']) ?></strong></td>
                                    <td><small><?= htmlspecialchars($row['email'] ?? 'brak') ?></small></td>
                                    <td>
                                        <a href="?action=delete_operator&operator=<?= urlencode($row['operator']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Usunąć operatora?')">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="panel">
                <div class="panel-title">
                    <i class="fa-solid fa-cog"></i>
                    Ustawienia
                </div>

                <div class="panel-body">
                    <a href="logout.php" class="btn btn-warning w-100">
                        <i class="fa-solid fa-right-from-bracket"></i>
                        Wyloguj admina
                    </a>
                </div>
            </div>
        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
