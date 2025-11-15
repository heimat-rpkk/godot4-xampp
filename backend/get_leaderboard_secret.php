<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "godot_test";
$port = 3307; // default is 3306
$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
 die("Connection failed: " . $conn->connect_error);
}


// $sql = "SELECT user_id, MAX(score) AS score
//         FROM scores_secrets
//         GROUP BY user_id
//         ORDER BY score DESC
//         LIMIT 10;";

$sql = "SELECT s.username, x.score
        FROM (
                SELECT user_id, MAX(score) AS score
                FROM scores_secrets
                GROUP BY user_id
        ) AS x
        JOIN secrets AS s ON x.user_id = s.id
        ORDER BY x.score DESC
        LIMIT 10;";

$result = $conn->query($sql);
//luo tyhjä taulukkomuuttuja 
$leaderboard = [];
//niin kauan kun on kyselyyn täsmääviä tuloksia( rivejä), lisää talukkoon
while ($row = $result->fetch_assoc()) {
 $leaderboard[] = $row;
}
//palautetaan data JSON-muodossa
header('Content-Type: application/json');
echo json_encode($leaderboard);
$conn->close();
?>
