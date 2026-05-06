<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireLogin();

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':   listFeedback(); break;
    case 'submit': submitFeedback(); break;
}

function listFeedback() {
    global $conn;
    $result = $conn->query("SELECT f.*, u.name AS customer_name, p.name AS package_name 
        FROM feedback f JOIN users u ON f.customer_id = u.id JOIN bookings b ON f.booking_id = b.id JOIN packages p ON b.package_id = p.id
        ORDER BY f.created_at DESC LIMIT 50");
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function submitFeedback() {
    global $conn;
    $user = currentUser();
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $stmt = $conn->prepare("INSERT INTO feedback (customer_id, booking_id, rating, comment) VALUES (?,?,?,?)");
    $stmt->bind_param("iiis", $user['id'], $data['booking_id'], $data['rating'], $data['comment']);
    echo json_encode(['success' => $stmt->execute()]);
}
?>