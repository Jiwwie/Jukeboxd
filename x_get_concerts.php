<?php
require_once '_functions.php';
$db = connectToDb();

$artist_id = $_GET['artist_id'] ?? '';

if (!$artist_id || !is_numeric($artist_id)) {
    echo json_encode([]);
    exit();
}

header('Content-Type: application/json');

$statement = $db->prepare("SELECT id, venue, city, date FROM jb_concerts WHERE artist_id = ? ORDER BY date DESC");
$statement->bind_param("i", $artist_id);
$statement->execute();
$result = $statement->get_result();

$concerts = [];
while ($row = $result->fetch_assoc()) {
    $concerts[] = $row;
}

echo json_encode($concerts);