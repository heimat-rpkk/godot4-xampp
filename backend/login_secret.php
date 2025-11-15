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
if (isset($data['username']) && isset($data['password'])) {
    $username = trim($data['username']);
    $password = trim($data['password']);

    // Tarkistus: käyttäjänimi ei saa olla tyhjä
    if ($username === '') {
        http_response_code(400);
        echo json_encode(["error" => "Käyttäjänimi ei saa olla tyhjä"]);
        exit;
    }

    // Tarkistus: salasana ei saa olla tyhjä
    if ($password === '') {
        http_response_code(400);
        echo json_encode(["error" => "Salasana ei saa olla tyhjä"]);
        exit;
    }

    // Tässä kohtaa data on validia — voit jatkaa tarkistusta
    // Haetaan tunnus/salasana JSON-datasta ja suojataan se SQL-injektiolta
    $username = $conn->real_escape_string($data['username']);
    $password = $conn->real_escape_string($data['password']);
    
    // Katsotaan löytyykö käyttäjä
    $stmt = $conn->prepare("SELECT id, passwd FROM secrets WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(["error" => "Väärä käyttäjätunnus tai salasana"]);
        exit();
    }

    $row = $result->fetch_assoc();
    $hash = $row["passwd"];

    // TÄRKEIN KOHTA → tarkistetaan hash
    if (password_verify($password, $hash)) {
        echo json_encode([
            "success" => true,
            "message" => "Kirjautuminen onnistui",
            "id" => $row["id"],
            "username" => $username
        ]);
    } else {
        echo json_encode(["error" => "Väärä käyttäjätunnus tai salasana"]);
    }

} else {
    // Jos data puuttuu, palautetaan virhe
    http_response_code(400);
    echo json_encode(["error" => "Pakolliset kentät puuttuvat"]);
}

$stmt->close();
$conn->close();
?>