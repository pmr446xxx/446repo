<?php
declare(strict_types=1);

if (!isset($_SESSION['operator'])) {
    header('Location: login.php');
    exit;
}

include 'includes/db.php';

$operator = $_SESSION['operator'];

$stmt = $pdo->prepare("SELECT id, channel, operator, correspondent, location_from, location_to, distance_km, comment, created_at FROM spots WHERE operator = ? ORDER BY created_at DESC");
$stmt->execute([$operator]);
$spots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<style>
.my-spots-wrapper {
    width: 100% !important;
    background: #000 !important;
    padding: 20px !important;
    margin: 0 !important;
}

.my-spots-container {
    max-width: 1200px !important;
    margin: 0 auto !important;
}

.my-spots-header {
    display: grid !important;
    grid-template-columns: 1fr auto !important;
    gap: 20px !important;
    align-items: center !important;
    border-bottom: 2px solid #ff3b3b !important;
    padding-bottom: 15px !important;
    margin-bottom: 20px !important;
}

.my-spots-header h2 {
    margin: 0 !important;
    color: #ff3b3b !important;
    font-weight: 700 !important;
    font-size: 1.3rem !important;
}

.my-spots-header p {
    margin: 0 !important;
    color: #9ca3af !important;
    font-size: 0.9rem !important;
    white-space: nowrap !important;
}

.spots-grid {
    display: grid !important;
    gap: 20px !important;
    margin-bottom: 30px !important;
}

.spot-card {
    background: transparent !important;
    border: 2px solid #ff3b3b !important;
    border-radius: 6px !important;
    padding: 20px !important;
    transition: all 0.3s ease !important;
}

.spot-card:hover {
    box-shadow: 0 0 20px rgba(255, 59, 59, 0.3) !important;
    transform: translateY(-2px) !important;
}

.spot-card-header {
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
    margin-bottom: 15px !important;
    padding-bottom: 10px !important;
    border-bottom: 1px solid rgba(255, 59, 59, 0.3) !important;
}

.spot-number {
    font-size: 1.4rem !important;
    font-weight: 900 !important;
    color: #ff3b3b !important;
}

.spot-channel {
    background: rgba(255, 59, 59, 0.1) !important;
    border: 1px solid rgba(255, 59, 59, 0.5) !important;
    color: #ff3b3b !important;
    padding: 6px 12px !important;
    border-radius: 4px !important;
    font-weight: 700 !important;
    font-size: 0.85rem !important;
}

.spot-route {
    display: grid !important;
    grid-template-columns: 1fr auto 1fr !important;
    gap: 12px !important;
    margin-bottom: 15px !important;
    align-items: center !important;
    min-height: 50px !important;
    width: 100% !important;
}

.route-item {
    display: flex !important;
    flex-direction: column !important;
    justify-content: center !important;
    align-items: flex-start !important;
}

.route-label {
    color: #9ca3af !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    margin-bottom: 4px !important;
    display: block !important;
    height: 12px !important;
}

.route-value {
    color: #fff !important;
    font-weight: 600 !important;
    font-size: 1rem !important;
    line-height: 1.2 !important;
}

.route-arrow {
    color: #ff3b3b !important;
    font-size: 1.5rem !important;
    font-weight: 900 !important;
    text-align: center !important;
    height: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
}

.spot-details {
    display: grid !important;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)) !important;
    gap: 15px !important;
    margin-bottom: 15px !important;
    padding-bottom: 15px !important;
    border-bottom: 1px solid rgba(255, 59, 59, 0.3) !important;
}

.detail-item {
    display: flex !important;
    flex-direction: column !important;
}

.detail-label {
    color: #9ca3af !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    margin-bottom: 4px !important;
}

.detail-value {
    color: #fff !important;
    font-weight: 600 !important;
}

.detail-value.distance {
    color: #22c55e !important;
    font-size: 1.1rem !important;
}

.detail-value.comment {
    color: #fbbf24 !important;
}

.spot-actions {
    display: flex !important;
    gap: 10px !important;
    justify-content: flex-end !important;
}

.btn-spot-edit, .btn-spot-delete {
    padding: 10px 16px !important;
    border: none !important;
    border-radius: 4px !important;
    cursor: pointer !important;
    font-weight: 600 !important;
    font-size: 0.9rem !important;
    display: inline-flex !important;
    align-items: center !important;
    gap: 6px !important;
    transition: all 0.3s !important;
}

.btn-spot-edit {
    background: rgba(59, 130, 246, 0.8) !important;
    color: #fff !important;
}

.btn-spot-edit:hover {
    background: rgba(37, 99, 235, 1) !important;
    transform: translateY(-2px) !important;
}

.btn-spot-delete {
    background: rgba(239, 68, 68, 0.8) !important;
    color: #fff !important;
}

.btn-spot-delete:hover {
    background: rgba(220, 38, 38, 1) !important;
    transform: translateY(-2px) !important;
}

.no-spots {
    text-align: center !important;
    padding: 40px 20px !important;
    color: #9ca3af !important;
}

.no-spots i {
    font-size: 3rem !important;
    margin-bottom: 15px !important;
    opacity: 0.5 !important;
}

.no-spots p {
    margin: 10px 0 !important;
}

.add-spot-btn {
    display: inline-block !important;
    background: rgba(59, 130, 246, 0.8) !important;
    color: #fff !important;
    padding: 12px 20px !important;
    border-radius: 4px !important;
    text-decoration: none !important;
    font-weight: 600 !important;
    margin-top: 15px !important;
    transition: all 0.3s !important;
}

.add-spot-btn:hover {
    background: rgba(37, 99, 235, 1) !important;
    text-decoration: none !important;
}
</style>
</head>
<body>

<div class="my-spots-wrapper">
    <div class="my-spots-container">
        <div class="my-spots-header">
            <h2><i class="fa-solid fa-list"></i> <?= t('my_spots') ?></h2>
            <p><?= t('no_spots') ?>: <?= count($spots) ?></p>
        </div>

        <?php if (count($spots) > 0): ?>
            <div class="spots-grid">
                <?php foreach ($spots as $index => $spot): ?>
                    <div class="spot-card">
                        <div class="spot-card-header">
                            <div class="spot-number">#<?= (int)$spot['id'] ?></div>
                            <div class="spot-channel">CH <?= (int)$spot['channel'] ?></div>
                        </div>

                        <div class="spot-route">
                            <div class="route-item">
                                <span class="route-label"><?= t('from') ?></span>
                                <div class="route-value"><?= htmlspecialchars((string)$spot['location_from']) ?></div>
                            </div>
                            <div class="route-arrow">→</div>
                            <div class="route-item">
                                <span class="route-label"><?= t('to') ?></span>
                                <div class="route-value"><?= htmlspecialchars((string)$spot['location_to']) ?></div>
                            </div>
                        </div>

                        <div class="spot-details">
                            <div class="detail-item">
                                <span class="detail-label"><?= t('correspondent') ?></span>
                                <span class="detail-value"><?= htmlspecialchars((string)$spot['correspondent']) ?></span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><?= t('km') ?></span>
                                <span class="detail-value distance"><?= (int)$spot['distance_km'] ?> km</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label"><?= t('added') ?></span>
                                <span class="detail-value"><?= date('Y-m-d H:i', strtotime((string)$spot['created_at'])) ?></span>
                            </div>
                            <?php if (!empty($spot['comment'])): ?>
                                <div class="detail-item">
                                    <span class="detail-label"><?= t('comment') ?></span>
                                    <span class="detail-value comment"><?= htmlspecialchars((string)$spot['comment']) ?></span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="spot-actions">
                            <a href="index.php?page=edit_spot&id=<?= (int)$spot['id'] ?>" class="btn-spot-edit">
                                <i class="fa-solid fa-pen-to-square"></i> <?= t('edit') ?? 'Edytuj' ?>
                            </a>
                            <button class="btn-spot-delete" onclick="deleteMySpot(<?= (int)$spot['id'] ?>)">
                                <i class="fa-solid fa-trash"></i> <?= t('delete') ?? 'Usuń' ?>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="no-spots">
                <i class="fa-solid fa-inbox"></i>
                <p><?= t('no_spots_message') ?? t('no_spots') ?></p>
                <a href="spot_add.php" class="add-spot-btn">
                    <i class="fa-solid fa-plus"></i> <?= t('add_spot') ?>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>

<script>
function deleteMySpot(id) {
    if (!confirm('<?= t('delete') ?? 'Usunąć' ?> spot #' + id + '?')) return;
    fetch('index.php?ajax=delete_spot', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id,
        cache: 'no-store'
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            location.reload();
        }
    })
    .catch(() => alert('Error'));
}
</script>
