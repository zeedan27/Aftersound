<?php
// Backend: Add friend handler (placeholder)
session_start();
require_once __DIR__ . '/config/db.php';

// Expect POST: friend_id
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // TODO: implement add friend logic
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Not implemented']);
    exit;
}
http_response_code(405);
