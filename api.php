<?php
session_start();
$upload_dir = 'uploads/';
$json_file = 'content.json';

// Sicherheits-Check: Nur eingeloggte User dürfen speichern!
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    http_response_code(403);
    die("Nicht eingeloggt");
}

// A. TEXTE SPEICHERN
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['json_data'])) {
    $data = $_POST['json_data'];
    // Validieren ob es valides JSON ist
    if(json_decode($data) != null) {
        file_put_contents($json_file, $data);
        echo "Gespeichert";
    } else {
        http_response_code(400);
        echo "Fehlerhafte Daten";
    }
    exit;
}

// B. BILD UPLOAD
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    $filename = time() . "_" . basename($_FILES['file']['name']); // Timestamp um Caching zu verhindern
    $target = $upload_dir . $filename;
    
    if(move_uploaded_file($_FILES['file']['tmp_name'], $target)) {
        echo $filename; // Wir geben den neuen Dateinamen zurück an JS
    } else {
        http_response_code(500);
        echo "Upload Fehler";
    }
    exit;
}
?>