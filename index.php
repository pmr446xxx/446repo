<?php
declare(strict_types=1);

include 'includes/db.php';
include 'includes/lang.php';

$page = $_GET['page'] ?? 'home';

function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float {
    $earthRadius = 6371;
    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    return round($earthRadius * $c, 0);
}

function getKmColor($km): string {
    if ($km < 100) return '#a855f7';
    if ($km < 150) return '#f97316';
    return '#ef4444';
}

function getFlagSVG($code): string {
    $code = strtoupper($code);
    $flags = [
        'PL' => '<svg class="flag-pulse" width="24" height="16" viewBox="0 0 30 20"><rect width="30" height="10" fill="#FFFFFF"/><rect y="10" width="30" height="10" fill="#DC143C"/></svg>',
        'DE' => '<svg class="flag-pulse" width="24" height="16" viewBox="0 0 30 20"><rect width="30" height="6.67" fill="#000000"/><rect y="6.67" width="30" height="6.67" fill="#DD0000"/><rect y="13.33" width="30" height="6.67" fill="#FFCE00"/></svg>',
        'CZ' => '<svg class="flag-pulse" width="24" height="16" viewBox="0 0 30 20"><rect width="30" height="10" fill="#FFFFFF"/><rect y="10" width="30" height="10" fill="#DC143C"/><polygon points="0,0 15,10 0,20" fill="#11457E"/></svg>',
        'SK' => '<svg class="flag-pulse" width="24" height="16" viewBox="0 0 30 20"><rect width="30" height="6.67" fill="#FFFFFF"/><rect y="6.67" width="30" height="6.67" fill="#EE334E"/><rect y="13.33" width="30" height="6.67" fill="#2B579A"/></svg>',
        'EN' => '<svg class="flag-pulse" width="24" height="16" viewBox="0 0 60 40"><rect width="60" height="40" fill="#012169"/><path d="M0,0 L60,40 M60,0 L0,40" stroke="#FFF" stroke-width="6"/><path d="M0,0 L60,40 M60,0 L0,40" stroke="#C8102E" stroke-width="4"/><path d="M30,0 V40 M0,20 H60" stroke="#FFF" stroke-width="10"/><path d="M30,0 V40 M0,20 H60" stroke="#C8102E" stroke-width="6"/></svg>',
        'FR' => '<svg class="flag-pulse" width="24" height="16" viewBox="0 0 30 20"><rect width="10" height="20" fill="#002395"/><rect x="10" width="10" height="20" fill="#FFFFFF"/><rect x="20" width="10" height="20" fill="#ED2939"/></svg>',
    ];
    return $flags[$code] ?? '<svg class="flag-pulse" width="24" height="16" viewBox="0 0 30 20"><rect width="30" height="20" fill="#999"/></svg>';
}

function formatTimeAgo($createdAt): string {
    $now = new DateTime();
    $created = new DateTime($createdAt);
    $diff = $now->diff($created);
    
    if ($diff->days > 0) {
        return $diff->days . 'd ' . $diff->h . 'h';
    } else {
        return $diff->h . 'h ' . $diff->i . 'm';
    }
}

if (isset($_GET['ajax'])) {
    if ($_GET['ajax'] === 'spots') {
        header('Content-Type: text/html; charset=utf-8');
        
        $currentOperator = $_SESSION['operator'] ?? null;
        $isAdmin = $currentOperator === 'admin';
        
        $search = $_GET['search'] ?? '';
        $limit = (int)($_GET['limit'] ?? 30);
        
        $query = "SELECT id, operator, correspondent, channel, location_from, location_to, distance_km, comment, created_at FROM spots WHERE 1=1";
        $params = [];
        
        if ($search) {
            $search = '%' . $search . '%';
            $query .= " AND (operator LIKE ? OR correspondent LIKE ? OR location_from LIKE ? OR location_to LIKE ? OR comment LIKE ? OR CAST(channel AS CHAR) LIKE ?)";
            $params = [$search, $search, $search, $search, $search, $search];
        }
        
        $query .= " ORDER BY id DESC LIMIT " . $limit;
        
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $spotOperator = (string)$row['operator'];
            $canEdit = $isAdmin || ($currentOperator && $currentOperator === $spotOperator);
            
            $countryCode = 'PL';
            try {
                $stmtOp = $pdo->prepare("SELECT country_code FROM operators WHERE operator = ? LIMIT 1");
                $stmtOp->execute([$spotOperator]);
                $opResult = $stmtOp->fetch(PDO::FETCH_ASSOC);
                if ($opResult && $opResult['country_code']) {
                    $countryCode = strtoupper($opResult['country_code']);
                }
            } catch (Throwable $e) {
                //
            }

            $flagSVG = getFlagSVG($countryCode);
            $timeAgo = formatTimeAgo((string)$row['created_at']);

            $distanceKm = (int)$row['distance_km'];
            if ($distanceKm === 0 || $distanceKm === null) {
                try {
                    $stmtFrom = $pdo->prepare("SELECT lat, lng FROM cities WHERE city = ? LIMIT 1");
                    $stmtFrom->execute([(string)$row['location_from']]);
                    $cityFrom = $stmtFrom->fetch(PDO::FETCH_ASSOC);

                    $stmtTo = $pdo->prepare("SELECT lat, lng FROM cities WHERE city = ? LIMIT 1");
                    $stmtTo->execute([(string)$row['location_to']]);
                    $cityTo = $stmtTo->fetch(PDO::FETCH_ASSOC);

                    if ($cityFrom && $cityTo) {
                        $distanceKm = calculateDistance(
                            (float)$cityFrom['lat'],
                            (float)$cityFrom['lng'],
                            (float)$cityTo['lat'],
                            (float)$cityTo['lng']
                        );
                    }
                } catch (Throwable $e) {
                    //
                }
            }

            $hasComment = !empty($row['comment']);
            $kmColor = getKmColor($distanceKm);

            echo '<tr data-spot-id="'.(int)$row['id'].'" class="'.($hasComment ? 'has-comment' : '').'">
                <td class="col-time">'.date('H:i', strtotime((string)$row['created_at'])).'</td>
                <td class="col-op">
                    <div class="flag-wrap">'.$flagSVG.'</div>
                    <span class="opname">'.htmlspecialchars($spotOperator).'</span>
                </td>
                <td class="col-corr">'.htmlspecialchars((string)$row['correspondent']).'</td>
                <td class="col-ch">CH '.intval($row['channel']).'</td>
                <td class="col-mode">FM</td>
                <td class="col-from">'.htmlspecialchars((string)$row['location_from']).'</td>
                <td class="col-arrow">→</td>
                <td class="col-to">'.htmlspecialchars((string)$row['location_to']).'</td>
                <td class="col-km"><span style="color: '.$kmColor.';">'.$distanceKm.'</span> km</td>
                <td class="col-comment">'.htmlspecialchars((string)$row['comment']).'</td>
                <td class="col-time">'.$timeAgo.'</td>';
            
            if ($canEdit) {
                echo '<td class="col-act">
                    <a href="index.php?page=edit_spot&id='.(int)$row['id'].'" class="btn-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                    <a href="javascript:deleteSpot('.(int)$row['id'].');" class="btn-del"><i class="fa-solid fa-trash"></i></a>
                </td>';
            }
            
            echo '</tr>';
        }
        exit;
    }

    if ($_GET['ajax'] === 'delete_spot') {
        header('Content-Type: application/json; charset=utf-8');
        
        $spotId = (int)($_POST['id'] ?? 0);
        $currentOperator = $_SESSION['operator'] ?? null;
        
        if (!$spotId || !$currentOperator) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $stmt = $pdo->prepare("SELECT operator FROM spots WHERE id = ?");
        $stmt->execute([$spotId]);
        $spot = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$spot) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        if ($currentOperator !== 'admin' && $currentOperator !== $spot['operator']) {
            echo json_encode(['success' => false]);
            exit;
        }
        
        $stmt = $pdo->prepare("DELETE FROM spots WHERE id = ?");
        echo json_encode(['success' => $stmt->execute([$spotId])]);
        exit;
    }

    if ($_GET['ajax'] === 'users') {
        header('Content-Type: application/json; charset=utf-8');
        
        try {
            $stmt = $pdo->query("SELECT operator, qth, country_code FROM operators ORDER BY operator ASC LIMIT 20");
            $operators = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $totalUsers = $pdo->query("SELECT COUNT(*) as cnt FROM operators")->fetch(PDO::FETCH_ASSOC)['cnt'] ?? 0;
            echo json_encode(['success' => true, 'total_users' => (int)$totalUsers, 'operators' => $operators], JSON_UNESCAPED_UNICODE);
        } catch (Throwable $e) {
            echo json_encode(['success' => false]);
        }
        exit;
    }
}

include 'includes/header.php';
include 'includes/navbar.php';

if (($_GET['page'] ?? null) === 'my_profile') {
    include 'my_profile.php';
} elseif (($_GET['page'] ?? null) === 'my_spots') {
    include 'my_spots.php';
} elseif (($_GET['page'] ?? null) === 'spot_add') {
    include 'spot_add.php';
} elseif (($_GET['page'] ?? null) === 'edit_spot') {
    include 'edit_spot.php';
} else {
?>

<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
html, body { background: #000 !important; }

.main-wrapper { 
    width: 100%; 
    background: #000 !important; 
    padding: 20px; 
    margin: 0;
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 20px;
    align-items: flex-start;
}

.main-wrapper.not-logged {
    grid-template-columns: 1fr;
}

.panel { 
    background: transparent;
    border: none;
    padding: 0;
    margin: 0;
    display: flex;
    flex-direction: column;
}

.spoty-header-wrapper {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 20px;
}

.spoty-title {
    display: flex;
    align-items: center;
    gap: 12px;
    order: -1;
}

.spoty-title-text {
    color: #ff3b3b;
    font-size: 1.1rem;
    font-weight: 900;
    letter-spacing: 1px;
    text-transform: uppercase;
}

.spoty-title-indicator {
    width: 12px;
    height: 12px;
    background: #ff3b3b;
    border-radius: 50%;
    animation: pulse-indicator 1.5s ease-in-out infinite;
}

@keyframes pulse-indicator {
    0%, 100% { 
        box-shadow: 0 0 0 0 rgba(255, 59, 59, 0.7);
    }
    50% {
        box-shadow: 0 0 0 6px rgba(255, 59, 59, 0.3);
    }
}

.panel-title {
    background: transparent;
    border: none !important;
    padding: 0;
    font-weight: 700;
    color: #fff;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    font-size: 1rem;
    gap: 15px;
    flex-grow: 1;
}

.panel-title-right {
    display: flex;
    gap: 15px;
    align-items: center;
}

.search-container {
    display: flex;
    align-items: center;
    position: relative;
}

.search-toggle {
    background: none;
    border: none;
    color: #ff3b3b;
    font-size: 1.5rem;
    cursor: pointer;
    opacity: 1;
    transition: all 0.3s ease;
    padding: 5px 10px;
}

.search-toggle:hover {
    transform: scale(1.2);
    filter: drop-shadow(0 0 10px #ff3b3b);
}

.search-input-wrapper {
    display: none;
    position: absolute;
    top: 50px;
    left: 0;
    z-index: 100;
    animation: slideDown 0.3s ease-out;
    background: #000;
    padding: 15px;
    border: 1px solid rgba(255, 255, 255, 0.3);
    border-radius: 4px;
    gap: 8px;
}

.search-input-wrapper.active {
    display: flex;
}

.search-input-wrapper input {
    padding: 10px 12px;
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.3);
    color: #fff;
    border-radius: 4px;
    font-size: 0.85rem;
    width: 250px;
    transition: all 0.2s;
}

.search-input-wrapper input::placeholder {
    color: rgba(255, 255, 255, 0.5);
}

.search-input-wrapper input:focus {
    outline: none;
    border-color: #fff;
    background: rgba(255, 255, 255, 0.15);
}

.search-input-wrapper button {
    padding: 8px 12px;
    background: rgba(255, 255, 255, 0.8);
    border: none;
    color: #000;
    border-radius: 4px;
    cursor: pointer;
    font-weight: 700;
    font-size: 0.8rem;
    transition: background 0.2s;
}

.search-input-wrapper button:hover {
    background: #fff;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes flagPulse {
    0%, 100% { opacity: 1; filter: drop-shadow(0 0 0px rgba(255, 255, 255, 0)); }
    50% { opacity: 0.7; filter: drop-shadow(0 0 4px rgba(255, 255, 255, 0.5)); }
}

.panel-body { 
    padding: 0;
    border: 2px solid #fbbf24 !important;
    border-radius: 12px !important;
    overflow: hidden;
    min-height: 200px;
}

.table-responsive { 
    overflow-x: auto;
    margin: 0;
    padding: 0;
}

.table {
    color: #fff;
    background: transparent;
    margin: 0;
    width: 100%;
    border-collapse: collapse;
    border: none !important;
}

.table thead {
    background: transparent;
    border: none !important;
}

.table th {
    color: #fbbf24;
    font-weight: 400;
    border: none !important;
    padding: 12px 8px;
    text-transform: uppercase;
    font-size: 0.65rem;
    letter-spacing: 0.5px;
    text-align: center;
    background: transparent;
}

.table tbody {
    background: transparent;
}

.table tbody tr {
    background: transparent;
    border: none !important;
    transition: background 0.2s;
}

.table tbody tr:hover { 
    background: rgba(255, 255, 255, 0.05) !important;
}

.table tbody tr.has-comment { 
    background: rgba(251, 191, 36, 0.05) !important;
}

.table td {
    padding: 10px 6px;
    border: none !important;
    font-size: 0.88rem;
    text-align: center;
    vertical-align: middle;
    background: transparent;
    color: #fff;
}

.col-time { 
    color: #ff3b3b; 
    font-weight: 700;
    font-size: 0.9rem;
}

.col-op {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 8px 8px;
    text-align: center;
}

.flag-wrap {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.flag-wrap svg {
    border-radius: 2px;
    display: block;
}

.flag-pulse {
    animation: flagPulse 2s ease-in-out infinite;
}

.opname { 
    font-weight: 900; 
    color: #fff; 
    font-family: monospace; 
    font-size: 0.88rem; 
    text-transform: uppercase;
}

.col-corr { 
    color: #fff;
    font-weight: 500;
}

.col-from, .col-to { 
    color: #fff;
    font-weight: 500;
}

.col-ch { 
    color: #fff; 
    font-weight: 700;
}

.col-mode { 
    color: #fff; 
    font-weight: 600;
}

.col-arrow { 
    color: #ff3b3b; 
    font-weight: 900; 
    font-size: 1.2rem;
}

.col-km { 
    font-weight: 900;
    font-size: 0.95rem;
}

.col-comment { 
    color: #fff; 
    max-width: 140px;
}

.col-comment.has-comment { 
    color: #fbbf24; 
    font-weight: 600;
}

.col-time { 
    color: #fff; 
    font-size: 0.85rem;
}

.col-act {
    display: flex;
    gap: 6px;
    justify-content: center;
}

.btn-edit, .btn-del {
    padding: 5px 8px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 30px;
    height: 30px;
    font-size: 13px;
    opacity: 0.7;
    transition: all 0.15s;
    color: white;
}

.btn-edit:hover, .btn-del:hover {
    opacity: 1;
    transform: translateY(-2px);
}

.btn-edit {
    background: rgba(59, 130, 246, 0.7);
}

.btn-edit:hover {
    background: rgba(37, 99, 235, 1);
}

.btn-del {
    background: rgba(239, 68, 68, 0.7);
}

.btn-del:hover {
    background: rgba(220, 38, 38, 1);
}

.users-info {
    background: transparent;
    border: 2px solid #fbbf24 !important;
    border-radius: 12px !important;
    padding: 15px;
    height: auto;
    min-height: 165px;
    margin-top: 0;
}

.users-info:not(.visible) {
    display: none;
}

.users-header {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 0 0 12px 0;
    margin-bottom: 12px;
    border: none !important;
}

.users-header i {
    color: #22c55e;
    font-size: 1.2rem;
}

.users-header-title {
    color: #fff;
    font-weight: 700;
    font-size: 0.95rem;
}

.users-list {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 10px 0;
}

.user-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px;
    background: transparent;
    border-radius: 4px;
    transition: background 0.2s;
    color: #fff;
    font-size: 0.85rem;
}

.user-item:hover {
    background: rgba(255, 255, 255, 0.05);
}

.user-dot {
    width: 8px;
    height: 8px;
    background: #22c55e;
    border-radius: 50%;
    flex-shrink: 0;
}

.user-name {
    font-weight: 700;
    font-family: monospace;
    min-width: 80px;
}

.user-location {
    color: #fbbf24;
    font-size: 0.75rem;
    flex-grow: 1;
}

.user-channel {
    color: #999;
    font-size: 0.75rem;
}

.users-show-all {
    padding: 12px 0 0 0;
    border: none !important;
    margin-top: 12px;
    text-align: center;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.users-show-all button {
    background: none;
    border: none;
    color: #999;
    cursor: pointer;
    font-size: 0.8rem;
    padding: 12px 0;
    text-transform: uppercase;
    transition: color 0.2s;
}

.users-show-all button:hover {
    color: #fff;
}

.show-more-box {
    padding: 20px;
    text-align: center;
    border: 2px solid #fbbf24;
    border-radius: 8px;
    background: rgba(251, 191, 36, 0.05);
    margin-top: 10px;
    display: none;
}

.show-more-box.active {
    display: block;
}

.show-more-box button {
    background: #fbbf24;
    color: #000;
    border: none;
    padding: 12px 24px;
    border-radius: 6px;
    font-weight: 900;
    cursor: pointer;
    font-size: 0.95rem;
    transition: all 0.3s;
}

.show-more-box button:hover {
    background: #fcd34d;
    transform: scale(1.05);
}

@keyframes rowFadeIn {
    0% { opacity: 0; }
    100% { opacity: 1; }
}

tr.new-spot {
    animation: rowFadeIn 0.5s ease-out;
}

@media (max-width: 1024px) {
    .main-wrapper {
        grid-template-columns: 1fr;
    }
    
    .users-info {
        min-height: auto;
    }

    .spoty-header-wrapper {
        flex-direction: column;
        align-items: flex-start;
    }

    .panel-title {
        justify-content: flex-start;
    }
}
</style>

<div class="main-wrapper" id="mainWrapper">
    <div class="panel">
        <div class="spoty-header-wrapper">
            <div class="spoty-title">
                <div class="spoty-title-indicator"></div>
                <span class="spoty-title-text">SPOTY LIVE</span>
            </div>
            <div class="panel-title">
                <div class="panel-title-right">
                    <div class="search-container">
                        <button class="search-toggle" onclick="toggleSearch()" title="Szukaj spotów">
                            <i class="fa-solid fa-magnifying-glass"></i>
                        </button>
                        <div class="search-input-wrapper" id="searchWrapper">
                            <input type="text" id="searchInput" placeholder="<?php echo t('find_spot'); ?>" />
                            <button onclick="searchSpots()"><?php echo t('search_button'); ?></button>
                        </div>
                    </div>
                    <button id="autoRefreshBtn" type="button" class="btn btn-success btn-sm fw-bold">
                        <i class="fa-solid fa-rotate"></i> Auto-refresh ON
                    </button>
                </div>
            </div>
        </div>

        <div class="panel-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>UTC</th>
                            <th><?php echo t('operator'); ?></th>
                            <th><?php echo t('correspondent'); ?></th>
                            <th><?php echo t('ch'); ?></th>
                            <th><?php echo t('mode'); ?></th>
                            <th><?php echo t('from'); ?></th>
                            <th></th>
                            <th><?php echo t('to'); ?></th>
                            <th><?php echo t('km'); ?></th>
                            <th><?php echo t('comment'); ?></th>
                            <th><?php echo t('time'); ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="spotsTableBody">
                    </tbody>
                </table>
            </div>
        </div>

        <div class="show-more-box" id="showMoreBox">
            <button onclick="loadMoreSpots()">POKAŻ WIĘCEJ SPOTÓW</button>
        </div>
    </div>

    <div class="users-info" id="usersInfo">
        <div class="users-header">
            <i class="fa-solid fa-circle"></i>
            <span class="users-header-title"><?php echo t('operators_online'); ?> (<span id="counter-total">0</span>)</span>
        </div>
        <div class="users-list" id="usersList">
        </div>
        <div class="users-show-all">
            <button onclick="alert('Wszyscy operatorzy')">POKAŻ WSZYSTKICH</button>
        </div>
    </div>
</div>

<script>
let timer = null;
let autoRefreshOn = localStorage.getItem('autoRefreshOn') !== '0';
let lastSpotIds = [];
let currentSearch = '';
let searchOpen = false;
let isLoggedIn = <?php echo isset($_SESSION['operator']) ? 'true' : 'false'; ?>;
let currentLimit = 30;

function toggleSearch() {
    searchOpen = !searchOpen;
    const wrapper = document.getElementById('searchWrapper');
    wrapper.classList.toggle('active', searchOpen);
    if (searchOpen) {
        document.getElementById('searchInput').focus();
    }
}

function setRefreshButton() {
    const btn = document.getElementById('autoRefreshBtn');
    if (!btn) return;
    btn.innerHTML = autoRefreshOn 
        ? '<i class="fa-solid fa-rotate"></i> Auto-refresh ON' 
        : '<i class="fa-solid fa-pause"></i> Auto-refresh OFF';
    btn.classList.toggle('btn-success', autoRefreshOn);
    btn.classList.toggle('btn-danger', !autoRefreshOn);
}

function applyRefresh() {
    localStorage.setItem('autoRefreshOn', autoRefreshOn ? '1' : '0');
    setRefreshButton();
    if (timer) clearInterval(timer);
    if (autoRefreshOn) timer = setInterval(loadSpotsTable, 5000);
}

function loadUsers() {
    if (!isLoggedIn) return;
    
    fetch('index.php?ajax=users&_=' + Date.now(), { cache: 'no-store' })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                document.getElementById('counter-total').textContent = d.total_users;
                const usersList = document.getElementById('usersList');
                let html = '';
                (d.operators || []).slice(0, 8).forEach(op => {
                    let location = op.qth ? op.qth : 'Unknown';
                    location = location.charAt(0).toUpperCase() + location.slice(1).toLowerCase();
                    html += `<div class="user-item">
                        <div class="user-dot"></div>
                        <div class="user-name">${op.operator}</div>
                        <div class="user-location">${location}</div>
                    </div>`;
                });
                usersList.innerHTML = html;
            }
        })
        .catch(() => {});
}

function deleteSpot(id) {
    if (!confirm('Usunąć spot #' + id + '?')) return;
    fetch('index.php?ajax=delete_spot', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id=' + id,
        cache: 'no-store'
    })
    .then(r => r.json())
    .then(d => { if (d.success) loadSpotsTable(); })
    .catch(() => {});
}

function searchSpots() {
    currentSearch = document.getElementById('searchInput').value;
    currentLimit = 30;
    loadSpotsTable();
}

function loadMoreSpots() {
    currentLimit += 30;
    loadSpotsTable();
}

function loadSpotsTable() {
    let url = 'index.php?ajax=spots&limit=' + currentLimit + '&_=' + Date.now();
    if (currentSearch) {
        url += '&search=' + encodeURIComponent(currentSearch);
    }
    
    fetch(url, { cache: 'no-store' })
        .then(r => r.text())
        .then(html => {
            const tbody = document.getElementById('spotsTableBody');
            if (!tbody) return;
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newRows = tempDiv.querySelectorAll('tr');
            const newIds = Array.from(newRows).map(row => row.getAttribute('data-spot-id'));
            
            newRows.forEach(row => {
                if (!lastSpotIds.includes(row.getAttribute('data-spot-id'))) {
                    row.style.animation = 'rowFadeIn 0.5s ease-out';
                }
            });
            
            tbody.innerHTML = html;
            lastSpotIds = newIds;

            const showMoreBox = document.getElementById('showMoreBox');
            if (newRows.length >= 30) {
                showMoreBox.classList.add('active');
            } else {
                showMoreBox.classList.remove('active');
            }
        })
        .catch(() => {});
}

document.getElementById('autoRefreshBtn')?.addEventListener('click', () => {
    autoRefreshOn = !autoRefreshOn;
    applyRefresh();
});

document.getElementById('searchInput')?.addEventListener('keypress', (e) => {
    if (e.key === 'Enter') searchSpots();
});

if (isLoggedIn) {
    document.getElementById('usersInfo').classList.add('visible');
    document.getElementById('mainWrapper').classList.remove('not-logged');
} else {
    document.getElementById('usersInfo').classList.remove('visible');
    document.getElementById('mainWrapper').classList.add('not-logged');
}

applyRefresh();
loadSpotsTable();
loadUsers();
setInterval(loadUsers, 5000);
</script>

<?php
}

include 'includes/footer.php';
?>