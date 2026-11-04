<?php
declare(strict_types=1);

/*
 * 446CLUSTER — mapa propagacji troposferycznej
 * Osadzenie oficjalnej mapy Tropocast. Klucz API nie jest wymagany do iframe.
 * Geolokalizacja działa w przeglądarce na HTTPS po zgodzie użytkownika.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$operator = $_SESSION['operator'] ?? null;
?>
<!DOCTYPE html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="google" content="notranslate">
    <meta name="theme-color" content="#080d12">
    <title>Mapa Tropo | 446CLUSTER</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
          integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
    <style>
        :root {
            color-scheme: dark;
            --bg: #080d12;
            --panel: #111a22;
            --panel2: #15232d;
            --line: #273c49;
            --green: #22c55e;
            --green2: #16a34a;
            --text: #edf4f7;
            --muted: #9aabb6;
            --yellow: #fbbf24;
            --blue: #38bdf8;
        }
        * { box-sizing: border-box; }
        html, body { margin: 0; min-height: 100%; background: var(--bg); color: var(--text); font-family: Inter, "Segoe UI", Arial, sans-serif; }
        body { background-image: radial-gradient(ellipse at 50% -20%, #173040 0, transparent 50%); }
        a { color: var(--blue); text-decoration: none; }
        .shell { width: min(1800px, calc(100% - 28px)); margin: 0 auto; padding: 18px 0 30px; }
        .topbar { display:flex; align-items:center; justify-content:space-between; gap:18px; padding:14px 18px; background:rgba(15,25,34,.96); border:1px solid var(--line); border-radius:12px; box-shadow:0 12px 34px #0007; }
        .brand { display:flex; align-items:center; gap:13px; min-width:0; }
        .brand-mark { width:46px; height:46px; display:grid; place-items:center; border-radius:10px; color:#04120a; background:var(--green); font-weight:1000; font-size:19px; box-shadow:0 0 22px #22c55e33; }
        h1 { margin:0; font-size:clamp(18px,2.2vw,27px); letter-spacing:.04em; }
        .sub { color:var(--muted); font-size:12px; margin-top:4px; letter-spacing:.08em; }
        .clock { text-align:right; font-size:13px; font-weight:800; color:#b7c7d0; white-space:nowrap; }
        .clock strong { display:block; color:var(--green); font-size:15px; margin-top:4px; }
        .toolbar { display:flex; align-items:center; flex-wrap:wrap; gap:9px; margin:12px 0; padding:10px 12px; background:#0f1921; border:1px solid var(--line); border-radius:10px; }
        .status { display:flex; align-items:center; gap:8px; color:#c8d5dc; font-size:12px; font-weight:800; margin-right:auto; }
        .pulse { width:9px; height:9px; border-radius:50%; background:var(--green); box-shadow:0 0 0 5px #22c55e20; }
        .btn { border:1px solid #315166; color:#eaf5fa; background:#182a36; border-radius:7px; padding:9px 13px; cursor:pointer; font-weight:800; font-size:12px; transition:.15s ease; }
        .btn:hover { border-color:var(--green); background:#1d3541; }
        .btn.primary { background:var(--green2); border-color:var(--green2); color:#04140a; }
        .btn.primary:hover { background:var(--green); }
        .grid { display:grid; grid-template-columns:minmax(300px,.83fr) minmax(520px,1.55fr); gap:14px; align-items:stretch; }
        .card { min-width:0; background:rgba(14,23,31,.97); border:1px solid var(--line); border-radius:12px; overflow:hidden; box-shadow:0 10px 28px #0005; }
        .card-head { min-height:52px; padding:12px 15px; display:flex; align-items:center; justify-content:space-between; gap:12px; background:#13212b; border-bottom:1px solid var(--line); }
        .card-head h2 { font-size:14px; margin:0; letter-spacing:.04em; text-transform:uppercase; }
        .tag { color:#7ceaa1; background:#123222; border:1px solid #23623e; font-size:10px; font-weight:900; padding:5px 8px; border-radius:20px; white-space:nowrap; }
        #satelliteMap { height:620px; width:100%; background:#152430; }
        .map-footer { padding:12px 14px; display:flex; flex-wrap:wrap; justify-content:space-between; gap:8px; border-top:1px solid var(--line); color:var(--muted); font-size:11px; }
        .map-footer strong { color:#dce8ed; }
        .forecast-frame-wrap { height:620px; background:#09121a; }
        #forecastFrame { border:0; display:block; width:100%; height:100%; background:#09121a; }
        .hint { padding:12px 15px; border-top:1px solid var(--line); background:#101a22; color:var(--muted); font-size:11px; line-height:1.55; }
        .hint strong { color:#f3d27a; }
        .bottom { margin-top:14px; display:grid; grid-template-columns:1fr 1fr; gap:14px; }
        .info { border:1px solid var(--line); border-radius:10px; background:#0f1921; padding:14px 16px; color:var(--muted); font-size:12px; line-height:1.6; }
        .info h3 { color:#eaf2f6; font-size:12px; margin:0 0 6px; text-transform:uppercase; letter-spacing:.04em; }
        .legend { display:flex; flex-wrap:wrap; align-items:center; gap:7px; margin-top:8px; }
        .legend span { display:inline-flex; align-items:center; gap:5px; padding:5px 8px; border-radius:6px; background:#17242d; color:#d5e0e5; font-size:10px; }
        .swatch { width:8px; height:8px; border-radius:50%; display:inline-block; }
        .leaflet-container { font:12px Inter,"Segoe UI",Arial,sans-serif; }
        .leaflet-control-attribution { font-size:9px !important; }
        .loc-popup { color:#111; font-weight:700; }
        .footer { text-align:center; color:#647783; font-size:10px; padding:18px 0 0; }
        @media (max-width: 1050px) {
            .grid { grid-template-columns:1fr; }
            #satelliteMap, .forecast-frame-wrap { height: min(70vh, 560px); min-height:390px; }
        }
        @media (max-width: 620px) {
            .shell { width:calc(100% - 16px); padding-top:8px; }
            .topbar { padding:11px; align-items:flex-start; }
            .brand-mark { width:39px; height:39px; font-size:16px; }
            .clock { font-size:10px; }
            .clock strong { font-size:12px; }
            .toolbar { align-items:stretch; }
            .status { width:100%; padding:3px 0 6px; }
            .btn { flex:1; padding:10px 7px; }
            .bottom { grid-template-columns:1fr; }
            #satelliteMap, .forecast-frame-wrap { height:440px; min-height:440px; }
        }
    </style>
</head>
<body>
<div class="shell">
    <header class="topbar">
        <div class="brand">
            <div class="brand-mark">446</div>
            <div>
                <h1>MAPA PROPAGACJI TROPOSFERYCZNEJ</h1>
                <div class="sub">446CLUSTER · VHF / UHF · POLSKA</div>
            </div>
        </div>
        <div class="clock">
            <span id="dateText">Ładowanie daty…</span>
            <strong id="timeText">--:-- CET · CZAS POLSKI</strong>
        </div>
    </header>

    <div class="toolbar">
        <div class="status"><span class="pulse"></span><span id="locationStatus">Mapa Polski · oczekiwanie na zgodę lokalizacji</span></div>
        <button class="btn primary" id="locateBtn" type="button">⌖ WYKRYJ MOJĄ LOKALIZACJĘ</button>
        <button class="btn" id="polandBtn" type="button">CAŁA POLSKA</button>
        <button class="btn" id="refreshBtn" type="button">↻ ODŚWIEŻ PROGNOZĘ</button>
    </div>

    <main class="grid">
        <section class="card">
            <div class="card-head">
                <h2>Mapa satelitarna · Polska</h2>
                <span class="tag">GPS / AUTO-ZOOM</span>
            </div>
            <div id="satelliteMap" role="application" aria-label="Mapa satelitarna Polski z lokalizacją użytkownika"></div>
            <div class="map-footer">
                <span><strong id="coords">Pozycja: nieustalona</strong></span>
                <span>Widok startowy: cała Polska · po GPS: przybliżenie regionu</span>
            </div>
            <div class="hint">Lokalizacja jest pobierana przez przeglądarkę dopiero po wyrażeniu zgody. Strona 446CLUSTER pokazuje pozycję na tej mapie; nie wysyła jej automatycznie do serwisu Tropocast.</div>
        </section>

        <section class="card">
            <div class="card-head">
                <h2>Prognoza VHF / UHF · dane Tropocast</h2>
                <span class="tag">OKNO DO 72 H</span>
            </div>
            <div class="forecast-frame-wrap">
                <iframe id="forecastFrame"
                        src="https://tropocast.eu/map?lang=pl"
                        title="Interaktywna mapa propagacji troposferycznej Tropocast"
                        loading="eager"
                        allow="geolocation"
                        referrerpolicy="strict-origin-when-cross-origin"></iframe>
            </div>
            <div class="hint"><strong>Przybliżenie prognozy do Twojej pozycji:</strong> wewnątrz mapy Tropocast wybierz przycisk „Użyj GPS”. Przeglądarka nie pozwala stronie 446CLUSTER sterować mapą innego serwisu bezpośrednio. Dane prognozy są publikowane przez Tropocast; odświeżenie ramki pobiera najnowszy dostępny zestaw.</div>
        </section>
    </main>

    <div class="bottom">
        <section class="info">
            <h3>Legenda potencjału propagacyjnego</h3>
            Kolory na mapie Tropocast oznaczają modelowany potencjał atmosferyczny, a nie gwarantowany zasięg łączności.
            <div class="legend">
                <span><i class="swatch" style="background:#4774d8"></i> marginalny / słaby</span>
                <span><i class="swatch" style="background:#29b77b"></i> umiarkowany</span>
                <span><i class="swatch" style="background:#c6d843"></i> dobry</span>
                <span><i class="swatch" style="background:#f5a13b"></i> silny</span>
                <span><i class="swatch" style="background:#e34b4b"></i> bardzo silny</span>
                <span><i class="swatch" style="background:#e45bb4"></i> wyjątkowo silny</span>
            </div>
        </section>
        <section class="info">
            <h3>Ważne dla 144 MHz i 433 MHz</h3>
            Osadzona mapa pokazuje wspólny modelowany potencjał propagacji VHF/UHF. Nie rozdziela wyniku na osobną, skalibrowaną prognozę 144 MHz i 433 MHz. Rzeczywiste łączności zależą również od anten, wysokości, mocy, polaryzacji, terenu i zakłóceń.
        </section>
    </div>
    <div class="footer">446CLUSTER · Mapa wykorzystuje zewnętrzne kafelki satelitarne Esri i prognozę Tropocast · Lokalizacja wymaga HTTPS</div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script>
(function () {
    const map = L.map('satelliteMap', { scrollWheelZoom: true }).setView([52.05, 19.25], 6);
    const imagery = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        { maxZoom: 19, attribution: 'Tiles © Esri — Source: Esri, Maxar, Earthstar Geographics, and the GIS User Community' }
    ).addTo(map);
    const labels = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}',
        { maxZoom: 19, attribution: '' }
    ).addTo(map);
    const streets = L.tileLayer(
        'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
        { maxZoom: 19, attribution: '© OpenStreetMap contributors' }
    );
    L.control.layers(
        { 'Satelita': imagery, 'Mapa drogowa': streets },
        { 'Nazwy miejscowości i granice': labels },
        { collapsed: true }
    ).addTo(map);
    L.control.scale({ metric: true, imperial: false }).addTo(map);

    const polandBounds = L.latLngBounds([[49.0, 14.0], [54.9, 24.3]]);
    let userMarker = null;
    let userCircle = null;

    const status = document.getElementById('locationStatus');
    const coords = document.getElementById('coords');

    function showPosition(position) {
        const lat = position.coords.latitude;
        const lon = position.coords.longitude;
        const accuracy = Math.max(50, Math.round(position.coords.accuracy || 100));
        const point = [lat, lon];

        if (userMarker) map.removeLayer(userMarker);
        if (userCircle) map.removeLayer(userCircle);

        userMarker = L.marker(point).addTo(map);
        userMarker.bindPopup('<div class="loc-popup">Twoja lokalizacja<br>' +
            lat.toFixed(5) + ', ' + lon.toFixed(5) +
            '<br>Dokładność GPS: około ' + accuracy + ' m</div>').openPopup();
        userCircle = L.circle(point, {
            radius: accuracy,
            color: '#22c55e',
            weight: 2,
            fillColor: '#22c55e',
            fillOpacity: 0.13
        }).addTo(map);

        map.setView(point, 10, { animate: true });
        coords.textContent = 'GPS: ' + lat.toFixed(5) + '°N · ' + lon.toFixed(5) + '°E';
        status.textContent = 'Lokalizacja wykryta · mapa przybliżona do Twojego regionu';
    }

    function showLocationError(error) {
        const messages = {
            1: 'Brak zgody na lokalizację · mapa pokazuje całą Polskę',
            2: 'Pozycja niedostępna · sprawdź GPS lub ustawienia urządzenia',
            3: 'Przekroczono czas oczekiwania · kliknij „Wykryj moją lokalizację”'
        };
        status.textContent = messages[error && error.code] || 'Nie udało się wykryć lokalizacji';
        coords.textContent = 'Pozycja: nieustalona';
    }

    function locate() {
        if (!('geolocation' in navigator)) {
            status.textContent = 'Ta przeglądarka nie obsługuje geolokalizacji';
            return;
        }
        status.textContent = 'Oczekiwanie na zgodę i odczyt GPS…';
        navigator.geolocation.getCurrentPosition(
            showPosition,
            showLocationError,
            { enableHighAccuracy: true, timeout: 15000, maximumAge: 120000 }
        );
    }

    function updateClock() {
        const now = new Date();
        const date = new Intl.DateTimeFormat('pl-PL', {
            timeZone: 'Europe/Warsaw',
            weekday: 'long', day: '2-digit', month: 'long', year: 'numeric'
        }).format(now);
        const time = new Intl.DateTimeFormat('pl-PL', {
            timeZone: 'Europe/Warsaw', hour: '2-digit', minute: '2-digit', second: '2-digit'
        }).format(now);
        document.getElementById('dateText').textContent = date.toUpperCase();
        document.getElementById('timeText').textContent = time + ' CET/CEST · CZAS POLSKI';
    }

    document.getElementById('locateBtn').addEventListener('click', locate);
    document.getElementById('polandBtn').addEventListener('click', function () {
        map.fitBounds(polandBounds, { padding: [14, 14] });
        status.textContent = 'Widok całej Polski';
    });
    document.getElementById('refreshBtn').addEventListener('click', function () {
        const frame = document.getElementById('forecastFrame');
        frame.src = frame.src;
        status.textContent = 'Odświeżono osadzoną mapę prognozy Tropocast';
    });

    updateClock();
    window.setInterval(updateClock, 1000);
    map.fitBounds(polandBounds, { padding: [12, 12] });

    // Próba lokalizacji po wejściu na stronę — przeglądarka zapyta o zgodę.
    locate();
})();
</script>
</body>
</html>
