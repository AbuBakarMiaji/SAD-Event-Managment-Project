<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireLogin();

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':  listPayments(); break;
    case 'pay':   makePayment();  break;
    case 'stats': paymentStats(); break;
}

function listPayments() {
    global $conn;
    $user = currentUser();
    if ($user['role'] === 'customer') {
        $stmt = $conn->prepare("SELECT py.*, b.event_date, p.name AS package_name 
            FROM payments py JOIN bookings b ON py.booking_id = b.id JOIN packages p ON b.package_id = p.id
            WHERE b.customer_id = ? ORDER BY py.created_at DESC");
        $stmt->bind_param("i", $user['id']);
    } else {
        $stmt = $conn->prepare("SELECT py.*, b.event_date, p.name AS package_name, c.name AS customer_name 
            FROM payments py JOIN bookings b ON py.booking_id = b.id JOIN packages p ON b.package_id = p.id JOIN users c ON b.customer_id = c.id
            ORDER BY py.created_at DESC");
    }
    $stmt->execute();
    $rows = [];
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function makePayment() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $stmt = $conn->prepare("UPDATE payments SET status='paid', method=?, paid_at=NOW() WHERE booking_id=?");
    $stmt->bind_param("si", $data['method'], $data['booking_id']);
    echo json_encode(['success' => $stmt->execute()]);
}

function paymentStats() {
    global $conn;
    $result = $conn->query("SELECT SUM(amount) AS total, COUNT(*) AS count FROM payments WHERE status='paid'");
    $row = $result->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $row]);
}
?>