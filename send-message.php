<?php
// Backend: Send message handler (placeholder)
session_start();
require_once __DIR__ . '/config/db.php';

// Expect POST: to_user, message, themeData?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: insert message into DB and return status
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not implemented']);
    exit;
}
http_response_code(405);
