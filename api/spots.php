<?php

require_once("../includes/db.php");

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