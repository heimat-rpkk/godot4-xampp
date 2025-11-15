<?php
// Tietokantayhteys
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "godot_test";
$port = 3307;

$conn = new mysqli($servername, $username, $password, $dbname, $port);

if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Tietokantayhteys epäonnistui"]);
    exit();
}

// Luetaan JSON-data requestista
$input = file_get_contents("php://input");
$data = json_decode($input, true);

if (!$data) {
    http_response_code(400);
    echo json_encode(["error" => "Virheellinen JSON-data"]);
    exit();
}

// Otetaan arvot
$user = $data["username"] ?? null;
$name = $data["fullname"] ?? null;
$email = $data["email"] ?? null;
$pass = $data["passwd"] ?? null;

if (!$user || !$name || !$email || !$pass) {
    http_response_code(400);
    echo json_encode(["error" => "Kaikki kentät vaaditaan"]);
    exit();
}
// Tarkistetaan, onko käyttäjätunnus jo olemassa
$stmt = $conn->prepare("SELECT id FROM secrets WHERE username = ?");
$stmt->bind_param("s", $user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    http_response_code(409); // Conflict
    echo json_encode(["error" => "Käyttäjätunnus on jo käytössä"]);
    $stmt->close();
    $conn->close();
    exit();
}
$stmt->close();
 // Hashataan salasana
$hash = password_hash($pass, PASSWORD_DEFAULT);

// SQL prepared statement
$stmt = $conn->prepare("INSERT INTO secrets (username, fullname, email, passwd) VALUES (?, ?, ?, ?)");


if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "SQL prepare epäonnistui"]);
    exit();
}
$stmt->bind_param("ssss", $user, $name, $email, $hash);


if ($stmt->execute()) {
    $inserted_id = $stmt->insert_id; // Haetaan lisätyn rivin id
    echo json_encode(["success" => true, "message" => "Käyttäjä rekisteröity!", "id" => $inserted_id, "username" =>$user]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Rekisteröinti epäonnistui"]);
}

$stmt->close();
$conn->close();
?>
