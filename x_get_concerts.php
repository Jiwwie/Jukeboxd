<?php
require_once '_functions.php';
$db = connectToDb();

$artist_id = $_GET['artist_id'] ?? '';

if (!$artist_id || !is_numeric($artist_id)) {
    echo json_encode([]);
    exit();
}

header('Content-Type: application/json');
echo json_encode(getConcertsByArtist($db, $artist_id));
