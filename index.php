<?php
declare(strict_types=1);

include 'includes/db.php';
include 'includes/lang.php';

/**
 * AJAX endpoint for fetching spots only
 */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'spots') {
    header('Content-Type: text/html; charset=utf-8');
    
    $currentOperator = $_SESSION['operator'] ?? null;
    $isAdmin = $currentOperator === 'admin';
    
    $stmt = $pdo->query("
        SELECT id, operator, correspondent, channel, location_from, location_to, distance_km, comment, created_at
        FROM spots
        ORDER BY id DESC
        LIMIT 50
    ");

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $spotOperator = (string)$row['operator'];
        $canEdit = $isAdmin || ($currentOperator && $currentOperator === $spotOperator);
        
        $now = new DateTime();
        $created = new DateTime((string)$row['created_at']);
        $diff = $now->diff($created);
        
        if ($diff->days > 0) {
            $timeAgo = $diff->days . "d " . $diff->h . "h temu";
        } elseif ($diff->h > 0) {
            $timeAgo = $diff->h . "h " . $diff->i . "m temu";
        } else {
            $timeAgo = $diff->i . "m temu";
        }
        
        echo '<tr data-spot-id="'.(int)$row['id'].'">
            <td>'.date('H:i', strtotime((string)$row['created_at'])).'</td>
            <td><strong>'.htmlspecialchars($spotOperator).'</strong></td>
            <td>'.htmlspecialchars((string)$row['correspondent']).'</td>
            <td>CH '.(int)$row['channel'].'</td>
            <td>FM</td>
            <td>'.htmlspecialchars((string)$row['location_from']).'</td>
            <td>'.htmlspecialchars((string)$row['location_to']).'</td>
            <td>'.(int)$row['distance_km'].' km</td>
            <td>'.htmlspecialchars((string)$row['comment']).'</td>
            <td>
                <small class="text-secondary">'.$timeAgo.'</small>';
        
        if ($canEdit) {
            echo ' <span class="spot-actions">
                <a href="edit_spot.php?id='.(int)$row['id'].'" class="spot-action-btn spot-edit-btn" title="Edytuj">
                    <i class="fa-solid fa-pen-to-square"></i>
                </a>
                <a href="delete_spot.php?id='.(int)$row['id'].'" class="spot-action-btn spot-delete-btn" title="Usuń" onclick="return confirm(\'Usunąć spot #'.(int)$row['id'].'?\');">
                    <i class="fa-solid fa-trash"></i>
                </a>
            </span>';
        }
        
        echo '</td>
        </tr>';
    }
    exit;
}

/**
 * AJAX map endpoint in this same file:
 * /index.php?ajax=map
 * Map shows only last 12 hours of connectivity.
 */
if (isset($_GET['ajax']) && $_GET['ajax'] === 'map') {
    header('Content-Type: application/json; charset=utf-8');

    function toAsciiPl(string $s): string {
        $map = [
            'ą'=>'a','ć'=>'c','ę'=>'e','ł'=>'l','ń'=>'n','ó'=>'o','ś'=>'s','ż'=>'z','ź'=>'z',
            'Ą'=>'a','Ć'=>'c','Ę'=>'e','Ł'=>'l','Ń'=>'n','Ó'=>'o','Ś'=>'s','Ż'=>'z','Ź'=>'z'
        ];
        return strtolower(strtr(trim($s), $map));
    }

    try {
        // ONLY last 12 hours for map lines
        $rows = $pdo->query("
            SELECT id, operator, channel, location_from, location_to, distance_km, created_at
            FROM spots
            WHERE created_at >= (NOW() - INTERVAL 12 HOUR)
            ORDER BY id DESC
            LIMIT 120
        ")->fetchAll(PDO::FETCH_ASSOC);

        $hasAscii = false;
        try {
            $c = $pdo->query("SHOW COLUMNS FROM cities LIKE 'city_ascii'")->fetch(PDO::FETCH_ASSOC);
            $hasAscii = (bool)$c;
        } catch (Throwable $e) {}

        if ($hasAscii) {
            $getCity = $pdo->prepare("
                SELECT city, lat, lng
                FROM cities
                WHERE LOWER(city)=LOWER(?)
                   OR city_ascii = ?
                ORDER BY CASE WHEN LOWER(city)=LOWER(?) THEN 0 ELSE 1 END
                LIMIT 1
            ");
        } else {
            $getCity = $pdo->prepare("
                SELECT city, lat, lng
                FROM cities
                WHERE LOWER(city)=LOWER(?)
                LIMIT 1
            ");
        }

        $cache = [];
        $items = [];

        foreach ($rows as $r) {
            $fromName = trim((string)$r['location_from']);
            $toName   = trim((string)$r['location_to']);

            if ($fromName === '' || $toName === '') continue;
            if ($fromName === '0' || $toName === '0') continue;

            if (!array_key_exists($fromName, $cache)) {
                if ($hasAscii) {
                    $getCity->execute([$fromName, toAsciiPl($fromName), $fromName]);
                } else {
                    $getCity->execute([$fromName]);
                }
                $cache[$fromName] = $getCity->fetch(PDO::FETCH_ASSOC) ?: null;
            }

            if (!array_key_exists($toName, $cache)) {
                if ($hasAscii) {
                    $getCity->execute([$toName, toAsciiPl($toName), $toName]);
                } else {
                    $getCity->execute([$toName]);
                }
                $cache[$toName] = $getCity->fetch(PDO::FETCH_ASSOC) ?: null;
            }

            $from = $cache[$fromName];
            $to   = $cache[$toName];
            if (!$from || !$to) continue;

            $fromLat = (float)$from['lat'];
            $fromLng = (float)$from['lng'];
            $toLat   = (float)$to['lat'];
            $toLng   = (float)$to['lng'];

            if (($fromLat == 0.0 && $fromLng == 0.0) || ($toLat == 0.0 && $toLng == 0.0)) continue;

            $items[] = [
                'id' => (int)$r['id'],
                'operator' => (string)$r['operator'],
                'channel' => (int)$r['channel'],
                'distance_km' => (int)$r['distance_km'],
                'created_at' => (string)$r['created_at'],
                'from' => ['city' => (string)$from['city'], 'lat' => $fromLat, 'lng' => $fromLng],
                'to'   => ['city' => (string)$to['city'],   'lat' => $toLat,   'lng' => $toLng],
            ];
        }

        echo json_encode([
            'success' => true,
            'count' => count($items),
            'items' => $items
        ], JSON_UNESCAPED_UNICODE);

    } catch (Throwable $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

include 'includes/header.php';
include 'includes/navbar.php';

$mySpots = [];
if (isset($_SESSION['operator'])) {
    $stmtMy = $pdo->prepare("
        SELECT id, correspondent, channel, location_from, location_to, distance_km, comment, created_at
        FROM spots
        WHERE operator = ?
        ORDER BY id DESC
        LIMIT 5
    ");
    $stmtMy->execute([$_SESSION['operator']]);
    $mySpots = $stmtMy->fetchAll(PDO::FETCH_ASSOC);
}

function formatTimeAgo(string $createdAt): string {
    $now = new DateTime();
    $created = new DateTime($createdAt);
    $diff = $now->diff($created);
    
    if ($diff->days > 0) {
        return $diff->days . "d " . $diff->h . "h temu";
    } elseif ($diff->h > 0) {
        return $diff->h . "h " . $diff->i . "m temu";
    } else {
        return $diff->i . "m temu";
    }
}
?>

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<style>
#plMap { height: 440px; border-radius: 10px; overflow: hidden; }
.map-note { font-size:12px; color:#9ca3af; margin-top:8px; }
.line-label { pointer-events: none; }

/* animated top banner on map */
.map-floating-banner {
    pointer-events: none;
    position: absolute;
    left: 50%;
    top: 14px;
    transform: translateX(-50%);
    z-index: 1000;
    background: rgba(0,0,0,.78);
    border: 1px solid #ff3b3b;
    color: #fff;
    border-radius: 12px;
    padding: 10px 16px;
    font-weight: 800;
    letter-spacing: .5px;
    text-shadow: 0 0 8px rgba(0,0,0,.8);
    box-shadow: 0 8px 24px rgba(0,0,0,.35);
    min-width: 320px;
    text-align: center;
    opacity: 0;
}
.map-floating-banner.show {
    animation: bannerInOut 4.2s ease-in-out forwards;
}
@keyframes bannerInOut {
    0%   { opacity: 0; transform: translate(-50%, -12px) scale(.96); }
    12%  { opacity: 1; transform: translate(-50%, 0) scale(1); }
    78%  { opacity: 1; transform: translate(-50%, 0) scale(1); }
    100% { opacity: 0; transform: translate(-50%, -8px) scale(.98); }
}
.banner-city { color: #7dd3fc; }
.banner-arrow { color: #fca5a5; margin: 0 8px; }
.banner-km { color: #fff; font-size: 1.05em; margin-left: 10px; }
.banner-dot { color: #f87171; margin: 0 8px; }

/* Spot actions animation - VERTICAL STACK, SMALL, TRANSPARENT */
.spot-actions {
    display: inline-flex;
    flex-direction: column;
    gap: 3px;
    align-items: center;
    animation: fadeInScale 0.5s ease-out;
    margin-left: 6px;
}
@keyframes fadeInScale {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    100% {
        opacity: 1;
        transform: scale(1);
    }
}
.spot-action-btn {
    padding: 2px 4px;
    font-size: 10px;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    transition: all 0.2s ease;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 20px;
    height: 20px;
    opacity: 0.6;
}
.spot-action-btn:hover {
    opacity: 1;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
}
.spot-edit-btn {
    background: rgba(59, 130, 246, 0.5);
    color: white;
}
.spot-edit-btn:hover {
    background: rgba(37, 99, 235, 0.8);
}
.spot-delete-btn {
    background: rgba(239, 68, 68, 0.5);
    color: white;
}
.spot-delete-btn:hover {
    background: rgba(220, 38, 38, 0.8);
}

/* Row fade-in for new spots */
@keyframes rowFadeIn {
    0% {
        opacity: 0;
        transform: translateX(-10px);
    }
    100% {
        opacity: 1;
        transform: translateX(0);
    }
}
tr.new-spot {
    animation: rowFadeIn 0.5s ease-out;
}
</style>

<div class="container-fluid mt-4">
    <div class="row g-3">

        <div class="col-xl-9 col-lg-8">
            <div class="panel">
                <div class="panel-title">
                    <div class="panel-title-left">
                        <i class="fa-solid fa-satellite-dish"></i>
                        <span><?= t('live_spots') ?></span>
                    </div>

                    <button id="autoRefreshBtn" type="button" class="btn btn-success btn-sm fw-bold">
                        <?= t('auto_refresh_on') ?>
                    </button>
                </div>

                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th><?= t('utc') ?></th>
                                    <th><?= t('operator') ?></th>
                                    <th><?= t('correspondent') ?></th>
                                    <th><?= t('ch') ?></th>
                                    <th><?= t('mode') ?></th>
                                    <th><?= t('from') ?></th>
                                    <th><?= t('to') ?></th>
                                    <th><?= t('km') ?></th>
                                    <th><?= t('comment') ?></th>
                                    <th><?= t('added') ?></th>
                                </tr>
                            </thead>
                            <tbody id="spotsTableBody">
                                <!-- AJAX loaded -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="panel mt-3">
                <div class="panel-title">
                    <div class="panel-title-left">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <span>Mapa połączeń (PL) — ostatnie 12h</span>
                    </div>
                </div>
                <div class="panel-body" id="mapPanelBody" style="position:relative;">
                    <div id="plMap"></div>
                    <div class="map-note">KM na mapie i w tabeli pochodzą z DB (distance_km) — spójnie i stale.</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-lg-4">

            <div class="panel">
                <div class="panel-title">
                    <div class="panel-title-left">
                        <i class="fa-solid fa-chart-column"></i>
                        <span><?= t('stats') ?></span>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="stats-list">
                        <div class="stats-row"><span><?= t('spots_today') ?></span><strong id="st_today">0</strong></div>
                        <div class="stats-row"><span><?= t('spots_month') ?></span><strong id="st_month">0</strong></div>
                        <div class="stats-row"><span><?= t('operators_today') ?></span><strong id="st_ops">0</strong></div>
                        <div class="stats-row"><span><?= t('channel_day') ?></span><strong id="st_ch">-</strong></div>
                        <div class="stats-row"><span><?= t('last_spot') ?></span><strong id="st_last"><?= t('none') ?></strong></div>
                    </div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-title">
                    <div class="panel-title-left">
                        <i class="fa-solid fa-bolt"></i>
                        <span><?= t('quick_actions') ?></span>
                    </div>
                </div>
                <div class="panel-body quick-actions">
                    <a href="spot_add.php" class="btn btn-success w-100 mb-2">
                        <i class="fa-solid fa-plus"></i> <?= t('add_spot') ?>
                    </a>
                    <a href="my_spots.php" class="btn btn-primary w-100 mb-2">
                        <i class="fa-solid fa-list"></i> <?= t('my_spots') ?>
                    </a>
                    <a href="profile.php" class="btn btn-warning w-100 mb-2">
                        <i class="fa-solid fa-id-card"></i> <?= t('my_profile') ?>
                    </a>
                    <?php if (isset($_SESSION['operator']) && $_SESSION['operator'] === 'admin'): ?>
                        <a href="admin.php" class="btn btn-danger w-100">
                            <i class="fa-solid fa-shield"></i> Panel Admina
                        </a>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (isset($_SESSION['operator'])): ?>
            <div class="panel">
                <div class="panel-title">
                    <div class="panel-title-left">
                        <i class="fa-solid fa-user-pen"></i>
                        <span><?= t('my_recent_spots') ?></span>
                    </div>
                </div>
                <div class="panel-body">
                    <?php if (empty($mySpots)): ?>
                        <p class="text-secondary mb-0"><?= t('no_spots') ?></p>
                    <?php else: ?>
                        <div class="my-spots-list">
                            <?php foreach ($mySpots as $s): ?>
                                <div class="my-spot-item">
                                    <div class="my-spot-top">
                                        <strong>#<?= (int)$s['id'] ?></strong>
                                        <span>CH <?= (int)$s['channel'] ?></span>
                                    </div>
                                    <div class="my-spot-mid">
                                        <?= htmlspecialchars((string)$s['correspondent']) ?> · <?= htmlspecialchars((string)$s['location_from']) ?> → <?= htmlspecialchars((string)$s['location_to']) ?>
                                    </div>
                                    <div class="my-spot-time">
                                        <?= date('H:i', strtotime((string)$s['created_at'])) ?> · <?= htmlspecialchars((string)$s['comment']) ?>
                                    </div>
                                    <div class="my-spot-actions">
                                        <a class="btn btn-primary btn-sm" href="edit_spot.php?id=<?= (int)$s['id'] ?>">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <a class="btn btn-danger btn-sm" href="delete_spot.php?id=<?= (int)$s['id'] ?>" onclick="return confirm('Usunąć spot #<?= (int)$s['id'] ?>?');">
                                            <i class="fa-solid fa-trash"></i>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <a href="my_spots.php" class="btn btn-warning w-100 mt-3"><?= t('manage_all') ?></a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
let timer = null;
let autoRefreshOn = localStorage.getItem('autoRefreshOn') !== '0';
let spotsRefreshTimer = null;
let lastSpotIds = [];
let mapZoomSet = false;

const TXT_ON  = <?= json_encode(t('auto_refresh_on')) ?>;
const TXT_OFF = <?= json_encode(t('auto_refresh_off')) ?>;

function setRefreshButton() {
    const btn = document.getElementById('autoRefreshBtn');
    if (!btn) return;
    btn.textContent = autoRefreshOn ? TXT_ON : TXT_OFF;
    btn.classList.toggle('btn-success', autoRefreshOn);
    btn.classList.toggle('btn-danger', !autoRefreshOn);
}

function applyRefresh() {
    localStorage.setItem('autoRefreshOn', autoRefreshOn ? '1' : '0');
    setRefreshButton();

    if (timer) clearInterval(timer);
    if (autoRefreshOn) {
        timer = setInterval(loadSpotsTable, 10000);
    }
}

function loadStats() {
    fetch('api/stats.php?_=' + Date.now(), { cache: 'no-store' })
        .then(r => r.json())
        .then(d => {
            if (!d.success) return;
            document.getElementById('st_today').textContent = d.spots_today;
            document.getElementById('st_month').textContent = d.spots_month;
            document.getElementById('st_ops').textContent = d.operators_today;
            document.getElementById('st_ch').textContent = d.top_channel;
            document.getElementById('st_last').textContent = d.last_spot_ago;
        })
        .catch(() => {});
}

function loadSpotsTable() {
    fetch('index.php?ajax=spots&_=' + Date.now(), { cache: 'no-store' })
        .then(r => r.text())
        .then(html => {
            const tbody = document.getElementById('spotsTableBody');
            if (!tbody) return;
            
            const tempDiv = document.createElement('div');
            tempDiv.innerHTML = html;
            const newRows = tempDiv.querySelectorAll('tr');
            
            // Get new spot IDs
            const newIds = Array.from(newRows).map(row => row.getAttribute('data-spot-id'));
            
            // Mark new rows
            newRows.forEach(row => {
                if (!lastSpotIds.includes(row.getAttribute('data-spot-id'))) {
                    row.classList.add('new-spot');
                }
            });
            
            tbody.innerHTML = html;
            lastSpotIds = newIds;
        })
        .catch(() => {});
}

document.getElementById('autoRefreshBtn')?.addEventListener('click', () => {
    autoRefreshOn = !autoRefreshOn;
    applyRefresh();
});

// ---- MAPA ----
let plMap = null;
let mapLayer = null;
const POLAND_BOUNDS = L.latLngBounds([49.0, 14.1], [54.9, 24.2]);

// Animated banner vars
let bannerEl = null;
let bannerTimer = null;
let bannerIndex = 0;
let bannerSignature = '';

function initMap() {
    plMap = L.map('plMap').setView([52.1, 19.4], 6);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 18,
        attribution: '&copy; OpenStreetMap'
    }).addTo(plMap);

    mapLayer = L.layerGroup().addTo(plMap);
}

function escapeHtml(s){
    return String(s)
        .replaceAll('&','&amp;')
        .replaceAll('<','&lt;')
        .replaceAll('>','&gt;')
        .replaceAll('"','&quot;')
        .replaceAll("'","&#039;");
}

function clampZoom(z, min, max) {
    return Math.max(min, Math.min(max, z));
}

function spacedKm(n) {
    const digits = String(Math.max(0, Number(n) || 0)).split('').join(' ');
    return digits + ' km';
}

function ensureBanner() {
    const panelBody = document.getElementById('mapPanelBody');
    if (!panelBody) return null;

    if (!bannerEl) {
        bannerEl = document.createElement('div');
        bannerEl.className = 'map-floating-banner';
        panelBody.appendChild(bannerEl);
    }
    return bannerEl;
}

function renderBannerItem(item) {
    const el = ensureBanner();
    if (!el || !item) return;

    const from = escapeHtml(item.from.city);
    const to   = escapeHtml(item.to.city);
    const km   = spacedKm(item.distance_km);

    el.innerHTML = `
      <span class="banner-city">${from}</span>
      <span class="banner-arrow">→</span>
      <span class="banner-city">${to}</span>
      <span class="banner-dot">•</span>
      <span class="banner-km">${km}</span>
    `;

    el.classList.remove('show');
    void el.offsetWidth;
    el.classList.add('show');
}

function startBannerLoop(items) {
    if (!Array.isArray(items) || items.length === 0) {
        if (bannerTimer) clearInterval(bannerTimer);
        return;
    }

    const sig = items.map(x => x.id).join(',');
    if (sig !== bannerSignature) {
        bannerSignature = sig;
        bannerIndex = 0;
    }

    if (bannerTimer) clearInterval(bannerTimer);

    renderBannerItem(items[bannerIndex % items.length]);
    bannerIndex++;

    bannerTimer = setInterval(() => {
        renderBannerItem(items[bannerIndex % items.length]);
        bannerIndex++;
    }, 5000);
}

// Oblicz maksymalny dystans między wszystkimi miastami
function calculateMaxDistance(items) {
    if (!items || items.length === 0) return 0;
    
    let maxDist = 0;
    items.forEach(item => {
        const lat1 = item.from.lat;
        const lng1 = item.from.lng;
        const lat2 = item.to.lat;
        const lng2 = item.to.lng;
        
        // Haversine formula
        const R = 6371; // km
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                  Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
                  Math.sin(dLng/2) * Math.sin(dLng/2);
        const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
        const dist = R * c;
        
        if (dist > maxDist) maxDist = dist;
    });
    
    return maxDist;
}

async function loadMapData() {
    if (!plMap || !mapLayer) return;
    mapLayer.clearLayers();

    try {
        const res = await fetch('index.php?ajax=map&_=' + Date.now(), { cache: 'no-store' });
        const data = await res.json();
        if (!data.success || !Array.isArray(data.items)) return;

        const items = data.items.slice(0, 30);
        const allBounds = [];
        let lineCount = 0;

        items.forEach((s, i) => {
            const from = [Number(s.from.lat), Number(s.from.lng)];
            const to   = [Number(s.to.lat), Number(s.to.lng)];
            if (!isFinite(from[0]) || !isFinite(from[1]) || !isFinite(to[0]) || !isFinite(to[1])) return;

            const isNewest = (i === 0);
            const ageFactor = Math.min(i / 10, 1);

            L.circleMarker(from, {
                radius: isNewest ? 7 : 5,
                color: '#22c55e',
                fillColor: '#22c55e',
                fillOpacity: isNewest ? 1 : (0.85 - ageFactor * 0.35),
                weight: isNewest ? 2 : 1
            }).addTo(mapLayer).bindTooltip('FROM: ' + escapeHtml(s.from.city));

            L.circleMarker(to, {
                radius: isNewest ? 7 : 5,
                color: '#3b82f6',
                fillColor: '#3b82f6',
                fillOpacity: isNewest ? 1 : (0.85 - ageFactor * 0.35),
                weight: isNewest ? 2 : 1
            }).addTo(mapLayer).bindTooltip('TO: ' + escapeHtml(s.to.city));

            const line = L.polyline([from, to], {
                color: isNewest ? '#ff1e1e' : '#ff6b6b',
                weight: isNewest ? 7 : Math.max(3, 5 - Math.floor(i / 6)),
                opacity: isNewest ? 1 : Math.max(0.35, 0.8 - ageFactor * 0.45),
                lineCap: 'round'
            }).addTo(mapLayer);

            if (isNewest) {
                let on = true;
                const pulse = setInterval(() => {
                    if (!plMap || !plMap.hasLayer(line)) { clearInterval(pulse); return; }
                    on = !on;
                    line.setStyle({ weight: on ? 8 : 6, opacity: on ? 1 : 0.6 });
                }, 650);
            }

            const centerLat = (from[0] + to[0]) / 2;
            const centerLng = (from[1] + to[1]) / 2;
            const kmText = spacedKm(s.distance_km);

            const kmIcon = L.divIcon({
                className: 'line-label',
                html: `<div style="
                    color:#ffffff;
                    font-weight:900;
                    font-size:${isNewest ? '18px' : '16px'};
                    letter-spacing:2px;
                    text-shadow:
                        -1px -1px 0 #000,
                         1px -1px 0 #000,
                        -1px  1px 0 #000,
                         1px  1px 0 #000,
                         0 0 8px rgba(0,0,0,0.9);
                    white-space:nowrap;
                    transform: translate(-50%, -50%);
                ">${kmText}</div>`,
                iconSize: [0,0]
            });

            L.marker([centerLat, centerLng], { icon: kmIcon, interactive: false }).addTo(mapLayer);

            line.bindPopup(
                `<b>${isNewest ? '🟢 NAJNOWSZA' : '#' + Number(s.id)}</b><br>` +
                `${escapeHtml(s.from.city)} → ${escapeHtml(s.to.city)}<br>` +
                `CH ${Number(s.channel)} | ${Number(s.distance_km)} km | ${escapeHtml(s.operator)}`
            );

            allBounds.push(from, to);
            lineCount++;
        });

        startBannerLoop(items);

        // FIXED ZOOM - ustal RAZ i nie zmieniaj
        if (!mapZoomSet && lineCount > 0) {
            const maxDistance = calculateMaxDistance(items);
            
            // Centrum mapy
            const allLats = [];
            const allLngs = [];
            items.forEach(item => {
                allLats.push(item.from.lat, item.to.lat);
                allLngs.push(item.from.lng, item.to.lng);
            });
            const centerLat = (Math.min(...allLats) + Math.max(...allLats)) / 2;
            const centerLng = (Math.min(...allLngs) + Math.max(...allLngs)) / 2;
            
            // Ustal zoom na podstawie dystansu
            let zoomLevel = 6; // default - Polska
            if (maxDistance < 140) {
                zoomLevel = 12; // Przybliż dla małych odległości
            } else if (maxDistance < 300) {
                zoomLevel = 10;
            } else if (maxDistance < 500) {
                zoomLevel = 8;
            }
            
            // Ustaw widok RAZ - bez zmiany
            plMap.setView([centerLat, centerLng], zoomLevel);
            mapZoomSet = true;
        }

    } catch (e) {
        console.error('Map load error:', e);
    }
}

loadStats();
applyRefresh();
loadSpotsTable();
setInterval(loadSpotsTable, 10000);

document.addEventListener('DOMContentLoaded', async () => {
    initMap();
    await loadMapData();
    setInterval(loadMapData, 10000);
});
</script>

<?php include 'includes/footer.php'; ?>
