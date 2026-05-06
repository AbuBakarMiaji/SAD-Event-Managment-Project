<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireLogin();

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':   listUsers();  break;
    case 'create': requireRole('admin'); createUser(); break;
    case 'update': requireRole('admin'); updateUser(); break;
    case 'delete': requireRole('admin'); deleteUser((int)($_GET['id'] ?? 0)); break;
    case 'me':     me(); break;
}

function listUsers() {
    global $conn;
    $role = $_GET['role'] ?? '';
    if ($role) {
        $stmt = $conn->prepare("SELECT id, name, email, phone, role, status, created_at FROM users WHERE role = ? ORDER BY created_at DESC");
        $stmt->bind_param("s", $role);
    } else {
        $stmt = $conn->prepare("SELECT id, name, email, phone, role, status, created_at FROM users ORDER BY created_at DESC");
    }
    $stmt->execute();
    $rows = [];
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function createUser() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role) VALUES (?,?,?,?,?)");
    $stmt->bind_param("sssss", $data['name'], $data['email'], $hashed, $data['phone'], $data['role']);
    echo json_encode(['success' => $stmt->execute(), 'id' => $conn->insert_id]);
}

function updateUser() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $stmt = $conn->prepare("UPDATE users SET name=?, email=?, phone=?, role=?, status=? WHERE id=?");
    $stmt->bind_param("sssssi", $data['name'], $data['email'], $data['phone'], $data['role'], $data['status'], $data['id']);
    echo json_encode(['success' => $stmt->execute()]);
}

function deleteUser($id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE users SET status='inactive' WHERE id=?");
    $stmt->bind_param("i", $id);
    echo json_encode(['success' => $stmt->execute()]);
}

function me() {
    echo json_encode(['success' => true, 'data' => currentUser()]);
}
?>