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
    
    // // Haetaan tietokannasta. **ei ole turvallista tallentaa selväkielisenä**
    $sql = "SELECT usrname, passwd FROM  users WHERE usrname LIKE '" . $username . "'";

    $result = $conn->query($sql);

    if ($result->num_rows === 0) {
    echo json_encode(["status" => "failed", "usr" => "none"]); //Käyttäjää ei ole olemassa
    } else {
        $user = $result->fetch_assoc();

        if (($password === $user['passwd'])) {
            echo json_encode(["status" => "succeeded"]);
        } else {
            echo json_encode(["status" => "failed"]); // väärä salasana
        }
    }
} else {
    // Jos data puuttuu, palautetaan virhe
    http_response_code(400);
    echo json_encode(["error" => "Pakolliset kentät puuttuvat"]);
}

$conn->close();
?>