<?php
//millä tunnuksilla ja mihin yhteys
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "godot_test";
$port = 3307;
$conn = new mysqli($servername, $username, $password, $dbname, $port);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
# isset = onko asetettu eli lähtetty
if (isset($_POST['username']) && isset($_POST['score'])) {
    $username = $_POST['username'];
    $score = intval($_POST['score']); // muuttaa luvuksi

    # luodaan SQL-lause, lisää mihin taulukkoo, mihin kenttiin, kysymysmerkkejä, niin monta kuin kenttiä
    $insert_sql = "INSERT INTO scores(username, score) VALUES (?,?)";
    $insert_stmt = $conn->prepare($insert_sql);
    // si = string, integer
    $insert_stmt->bind_param("si", $username, $score);
    if ($insert_stmt->execute()) {
    echo "inserted";
    } else {
    echo "error inserting score";
    }
    $insert_stmt->close();
}
$conn->close();
?>