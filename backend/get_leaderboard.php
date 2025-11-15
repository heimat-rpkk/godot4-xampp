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

// SQL-kysely: kaikki rivit, järjestetään ja rajataan 10
// $sql = "SELECT username, score 
//         FROM scores 
//         ORDER BY score 
//         DESC LIMIT 10;";
// SQL-kysely: haetaan käyttäjät ja hänen parhaat pisteet
$sql = "SELECT username, MAX(score) AS score
        FROM scores
        GROUP BY username
        ORDER BY score DESC
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
