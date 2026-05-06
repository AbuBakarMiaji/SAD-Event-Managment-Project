<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireLogin();

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':   listTasks();  break;
    case 'create': createTask(); break;
    case 'update': updateTask(); break;
}

function listTasks() {
    global $conn;
    $user = currentUser();
    if ($user['role'] === 'staff') {
        $stmt = $conn->prepare("SELECT t.*, b.event_date, p.name AS package_name, c.name AS customer_name 
            FROM tasks t JOIN bookings b ON t.booking_id = b.id JOIN packages p ON b.package_id = p.id JOIN users c ON b.customer_id = c.id
            WHERE t.staff_id = ? ORDER BY t.created_at DESC");
        $stmt->bind_param("i", $user['id']);
    } else {
        $stmt = $conn->prepare("SELECT t.*, b.event_date, p.name AS package_name, s.name AS staff_name 
            FROM tasks t JOIN bookings b ON t.booking_id = b.id JOIN packages p ON b.package_id = p.id JOIN users s ON t.staff_id = s.id
            ORDER BY t.created_at DESC");
    }
    $stmt->execute();
    $rows = [];
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function createTask() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $stmt = $conn->prepare("INSERT INTO tasks (booking_id, staff_id, title, description, service_type, due_date) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("iissss", $data['booking_id'], $data['staff_id'], $data['title'], $data['description'], $data['service_type'], $data['due_date']);
    echo json_encode(['success' => $stmt->execute(), 'id' => $conn->insert_id]);
}

function updateTask() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $user = currentUser();
    if ($user['role'] === 'staff') {
        $stmt = $conn->prepare("UPDATE tasks SET status=? WHERE id=? AND staff_id=?");
        $stmt->bind_param("sii", $data['status'], $data['id'], $user['id']);
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET status=?, title=?, description=? WHERE id=?");
        $stmt->bind_param("sssi", $data['status'], $data['title'], $data['description'], $data['id']);
    }
    echo json_encode(['success' => $stmt->execute()]);
}
?>