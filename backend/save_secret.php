<?php
// määrittää HTTP-vastauksen tyypin, jonka PHP lähettää
header('Content-Type: application/json; charset=utf-8');

// Tietokantayhteys
$servername = "localhost";
$username = "root";
$password = "";      // jos rootilla on salasana, lisää tähän
$dbname = "godot_test";
$port = 3307;        // mysql portti

$conn = new mysqli($servername, $username, $password, $dbname, $port);

// Tarkistus
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Yhteys epäonnistui: " . $conn->connect_error]);
    exit;
}

// Luetaan sisääntuleva JSON
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Virheellinen JSON-data"]);
    exit;
}

// Tarkistetaan ja käsitellään data
if (isset($data['user_id']) && isset($data['score'])) {
    $user_id = $data['user_id'];
    $score = $data['score'];

    // Tarkistus: user_id pitää olla numeerinen
    if (!is_numeric($user_id)) {
        http_response_code(400);
        echo json_encode(["error" => "user_id-arvon täytyy olla numero"]);
        exit;
    }

    // Tarkistus: score pitää olla numeerinen
    if (!is_numeric($score)) {
        http_response_code(400);
        echo json_encode(["error" => "Score-arvon täytyy olla numero"]);
        exit;
    }

    // Tässä kohtaa data on validia — voit jatkaa tallennukseen
    // Muutetaan user_id kokonaisluvuksi, jotta tietokantaan ei päädy tekstiä
    $user_id = (int)$data['user_id'];
    // Muutetaan pistearvo kokonaisluvuksi, jotta tietokantaan ei päädy tekstiä
    $score = (int)$data['score'];

    // Lisätään tietokantaan
    $sql = "INSERT INTO scores_secrets (user_id, score) VALUES ('$user_id', $score)";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["status" => "ok", "message" => "Rivi lisätty tietokantaan"]);
    } else {
        http_response_code(500);
        echo json_encode(["error" => "Virhe tallennuksessa tietokantaan: " . $conn->error]);
    }
} else {
    // Jos data puuttuu, palautetaan virhe
    http_response_code(400);
    echo json_encode(["error" => "Pakolliset kentät puuttuvat"]);
}

$conn->close();
?>
