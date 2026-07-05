<?php

include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

?>

<div class="container-fluid mt-4">

    <!-- TOP STATYSTYKI -->
    <div class="row top-stats-container">
        <div class="col-md-6 col-lg-3">
            <div class="top-stat-box">
                <div class="top-stat-box-icon red">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="top-stat-box-value" id="topStatOnline">0</div>
                <div class="top-stat-box-label">Operatorów Online</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="top-stat-box">
                <div class="top-stat-box-icon blue">
                    <i class="fa-solid fa-satellite-dish"></i>
                </div>
                <div class="top-stat-box-value" id="topStatSpots">0</div>
                <div class="top-stat-box-label">Spotów Działaj</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="top-stat-box">
                <div class="top-stat-box-icon yellow">
                    <i class="fa-solid fa-globe"></i>
                </div>
                <div class="top-stat-box-value" id="topStatCountries">0</div>
                <div class="top-stat-box-label">Kraje Aktywne</div>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="top-stat-box">
                <div class="top-stat-box-icon purple">
                    <i class="fa-solid fa-calendar-days"></i>
                </div>
                <div class="top-stat-box-value" id="topStatPlanned">0</div>
                <div class="top-stat-box-label">Planowanych Łączności</div>
            </div>
        </div>
    </div>

    <div class="row">

        <!-- LEWA STRONA - SPOTY LIVE -->
        <div class="col-lg-9">

            <!-- SPOTY LIVE -->
            <div class="panel">

                <div class="panel-title">
                    <div class="panel-title-left">
                        <i class="fa-solid fa-satellite-dish"></i>
                        <span>Spoty LIVE</span>
                    </div>
                    <div class="panel-title-right">
                        AUTO-ODŚWIEŻANIE: ON
                    </div>
                </div>

                <div class="panel-body">

                    <table class="table table-dark table-hover">

                        <thead>
                            <tr>
                                <th>UTC</th>
                                <th>Operator</th>
                                <th>CH</th>
                                <th>Mode</th>
                                <th>Z</th>
                                <th>Do</th>
                                <th>KM</th>
                                <th>Komentarz</th>
                                <th>Czas</th>
                            </tr>
                        </thead>

                        <tbody id="spotsLive">

                            <?php

                            $stmt = $pdo->query("
                                SELECT *
                                FROM spots
                                ORDER BY id DESC
                                LIMIT 50
                            ");

                            while($row = $stmt->fetch(PDO::FETCH_ASSOC))
                            {

                                echo '
                                    <tr data-id="'.$row["id"].'">
                                        <td>'.date("H:i",strtotime($row["created_at"])).'</td>
                                        <td><strong>'.$row["operator"].'</strong></td>
                                        <td>CH '.$row["channel"].'</td>
                                        <td>FM</td>
                                        <td>'.$row["location_from"].'</td>
                                        <td>'.$row["location_to"].'</td>
                                        <td>'.$row["distance_km"].' km</td>
                                        <td>'.$row["comment"].'</td>
                                        <td><i class="fa-solid fa-signal"></i></td>
                                    </tr>
                                ';

                            }

                            ?>

                        </tbody>

                    </table>

                </div>
            </div>

        </div>

        <!-- PRAWA STRONA - STATYSTYKI -->
        <div class="col-lg-3">

            <!-- OPERATORZY ONLINE -->
            <div class="panel">

                <div class="panel-title">
                    <div class="panel-title-left">
                        <i class="fa-solid fa-users"></i>
                        <span>Operatorzy Online (183)</span>
                    </div>
                </div>

                <div class="panel-body">

                    <div id="onlineOperators">
                        <p class="text-secondary">Ładowanie...</p>
                    </div>

                </div>

            </div>

            <!-- STATYSTYKI -->
            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-chart-simple"></i> Spotów działaj
                </div>
                <div class="stats-value" id="statToday">0</div>
                <div class="stats-card-info">w tym miesiącu</div>
            </div>

            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-radio"></i> Operatorów online
                </div>
                <div class="stats-value" id="statOnline">0</div>
                <div class="stats-card-info">aktualnie</div>
            </div>

            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-location-dot"></i> Kraje aktywne
                </div>
                <div class="stats-value" id="statCountries">0</div>
                <div class="stats-card-info">na całym świecie</div>
            </div>

            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-signal"></i> Kanał DX
                </div>
                <div class="stats-value">CH8</div>
                <div class="stats-card-info">główny kanał</div>
            </div>

        </div>

    </div>

</div>

<script>
// Funkcja do ładowania statystyk
function loadStats() {
    fetch('api/get_stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Top Statystyki
                document.getElementById('topStatOnline').textContent = data.online_operators;
                document.getElementById('topStatSpots').textContent = data.today_spots;
                document.getElementById('topStatCountries').textContent = data.active_locations;
                document.getElementById('topStatPlanned').textContent = 0;

                // Boczne statystyki
                document.getElementById('statToday').textContent = data.month_spots;
                document.getElementById('statOnline').textContent = data.online_operators;
                document.getElementById('statCountries').textContent = data.active_locations;

                // TOP Operatorzy
                let onlineHtml = '';
                if (data.top_operators && data.top_operators.length > 0) {
                    data.top_operators.slice(0, 10).forEach(op => {
                        onlineHtml += `<div class="online">${op.operator}</div>`;
                    });
                } else {
                    onlineHtml = '<p class="text-secondary">Brak operatorów online</p>';
                }
                document.getElementById('onlineOperators').innerHTML = onlineHtml;
            }
        })
        .catch(error => console.error('Error loading stats:', error));
}

// Załaduj statystyki na starcie i co 10 sekund
loadStats();
setInterval(loadStats, 10000);

// UTC Clock
function updateClock() {
    const now = new Date();
    const utcTime = now.toUTCString().split(' ')[4];
    const localTime = now.toLocaleTimeString('pl-PL');
    
    const utcElement = document.getElementById('utcClock');
    if (utcElement) {
        utcElement.textContent = utcTime;
    }
    
    const localElement = document.getElementById('localClock');
    if (localElement) {
        localElement.textContent = localTime;
    }
}

setInterval(updateClock, 1000);
updateClock();
</script>

<?php include 'includes/footer.php'; ?>
