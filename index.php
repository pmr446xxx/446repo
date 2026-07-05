<?php

include 'includes/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

?>

<div class="container-fluid mt-4">

    <div class="row">

        <!-- LEWA STRONA -->
        <div class="col-lg-9">

            <!-- SPOTY (STATIC / TEST) -->
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

<td>'.$row["operator"].'</td>

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

        <!-- PRAWA STRONA -->
        <div class="col-lg-3">

            <div class="panel">

                <div class="panel-title">
                    <i class="fa-solid fa-users"></i>
                    Operatorzy ONLINE
                </div>

                <div class="panel-body">

                    <div class="online">🟢 PMR001</div><br>
                    <div class="online">🟢 PMR155</div><br>
                    <div class="online">🟢 PMR777</div>

                </div>

            </div>

            <div class="stats-card">

                <div class="stats-title">
                    Statystyki
                </div>

                <div class="stats-value">
                    1
                </div>

                Spotów dzisiaj

            </div>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>