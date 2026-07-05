<?php

include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

?>

<div class="container-fluid mt-4">

    <div class="row">

        <!-- LEWA STRONA - SPOTY -->
        <div class="col-lg-9">

            <!-- SPOTY LIVE -->
            <div class="panel">

                <div class="panel-title">
                    <i class="fa-solid fa-satellite-dish"></i>
                    Spoty LIVE
                </div>

                <div class="panel-body">

                    <table class="table table-dark table-hover">

                        <thead>
                            <tr>
                                <th>Operator</th>
                                <th>Korespondent</th>
                                <th>Kanał</th>
                                <th>Wywołanie</th>
                                <th>Komentarz</th>
                                <th>Z</th>
                                <th>Do</th>
                                <th>Km</th>
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
                                        <td><strong>'.$row["operator"].'</strong></td>
                                        <td>'.$row["correspondent"].'</td>
                                        <td>CH '.$row["channel"].'</td>
                                        <td>'.$row["call_text"].'</td>
                                        <td>'.$row["comment"].'</td>
                                        <td>'.$row["location_from"].'</td>
                                        <td>'.$row["location_to"].'</td>
                                        <td>'.$row["distance_km"].' km</td>
                                        <td>'.date("H:i",strtotime($row["created_at"])).'</td>
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
                    <i class="fa-solid fa-users"></i>
                    Operatorzy ONLINE
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
                    <i class="fa-solid fa-chart-simple"></i> Spoty dzisiaj
                </div>
                <div class="stats-value" id="statToday">0</div>
                <div class="stats-card-info">dzisiejszych spot'ów</div>
            </div>

            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-calendar"></i> Spoty w miesiącu
                </div>
                <div class="stats-value" id="statMonth">0</div>
                <div class="stats-card-info">w tym miesiącu</div>
            </div>

            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-radio"></i> Kanały aktywne
                </div>
                <div class="stats-value" id="statChannels">0</div>
                <div class="stats-card-info">aktywnych kanałów</div>
            </div>

            <div class="stats-card">
                <div class="stats-title">
                    <i class="fa-solid fa-location-dot"></i> Lokacje
                </div>
                <div class="stats-value" id="statLocations">0</div>
                <div class="stats-card-info">unikalnych lokacji</div>
            </div>

            <!-- TOP OPERATORZY -->
            <div class="panel">

                <div class="panel-title">
                    <i class="fa-solid fa-crown"></i>
                    TOP Operatorzy
                </div>

                <div class="panel-body">

                    <div id="topOperators">
                        <p class="text-secondary">Ładowanie...</p>
                    </div>

                </div>

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
                document.getElementById('statToday').textContent = data.today_spots;
                document.getElementById('statMonth').textContent = data.month_spots;
                document.getElementById('statChannels').textContent = data.active_channels;
                document.getElementById('statLocations').textContent = data.active_locations;

                // TOP Operatorzy
                let topHtml = '';
                data.top_operators.forEach((op, index) => {
                    topHtml += `
                        <div class="online">
                            <strong>${index + 1}.</strong>&nbsp;
                            ${op.operator} <span style="margin-left: auto; color: var(--accent-red);">${op.count}</span>
                        </div>
                    `;
                });
                document.getElementById('topOperators').innerHTML = topHtml || '<p class="text-secondary">Brak danych</p>';

                // Operatorzy online
                let onlineHtml = '';
                const uniqueOperators = [...new Set(data.top_operators.map(op => op.operator))];
                uniqueOperators.slice(0, 10).forEach(op => {
                    onlineHtml += `<div class="online">${op}</div>`;
                });
                document.getElementById('onlineOperators').innerHTML = onlineHtml || '<p class="text-secondary">Brak operatorów online</p>';
            }
        })
        .catch(error => console.error('Error loading stats:', error));
}

// Załaduj statystyki na starcie i co 10 sekund
loadStats();
setInterval(loadStats, 10000);
</script>

<?php include 'includes/footer.php'; ?>
