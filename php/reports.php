<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireRole('admin');

$action = $_GET['action'] ?? 'summary';

switch ($action) {
    case 'summary': summary(); break;
    case 'bookings': bookingsReport(); break;
    case 'revenue':  revenueReport(); break;
    case 'users':    usersReport(); break;
}

function summary() {
    global $conn;
    $data = [];

    $data['total_users']    = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='customer'")->fetch_assoc()['c'];
    $data['total_bookings'] = $conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()['c'];
    $data['pending']        = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status='pending'")->fetch_assoc()['c'];
    $data['approved']       = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status='approved'")->fetch_assoc()['c'];
    $data['completed']      = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status='completed'")->fetch_assoc()['c'];
    $data['cancelled']      = $conn->query("SELECT COUNT(*) AS c FROM bookings WHERE status='cancelled'")->fetch_assoc()['c'];
    $data['total_revenue']  = $conn->query("SELECT COALESCE(SUM(amount),0) AS s FROM payments WHERE status='paid'")->fetch_assoc()['s'];
    $data['avg_rating']     = $conn->query("SELECT ROUND(AVG(rating),1) AS r FROM feedback")->fetch_assoc()['r'];
    $data['total_managers'] = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='manager'")->fetch_assoc()['c'];
    $data['total_staff']    = $conn->query("SELECT COUNT(*) AS c FROM users WHERE role='staff'")->fetch_assoc()['c'];

    echo json_encode(['success' => true, 'data' => $data]);
}

function bookingsReport() {
    global $conn;
    $result = $conn->query("SELECT b.id, c.name AS customer, p.name AS package, b.event_date, b.status, b.total_amount, b.created_at
        FROM bookings b JOIN users c ON b.customer_id = c.id JOIN packages p ON b.package_id = p.id
        ORDER BY b.created_at DESC");
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function revenueReport() {
    global $conn;
    $result = $conn->query("SELECT DATE_FORMAT(paid_at,'%Y-%m') AS month, SUM(amount) AS revenue 
        FROM payments WHERE status='paid' GROUP BY month ORDER BY month DESC LIMIT 12");
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function usersReport() {
    global $conn;
    $result = $conn->query("SELECT role, COUNT(*) AS count FROM users GROUP BY role");
    $rows = [];
    while ($r = $result->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}
?>