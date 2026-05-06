<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':
        getPackages();
        break;
    case 'get':
        getPackage((int)($_GET['id'] ?? 0));
        break;
    case 'create':
        requireRole('admin');
        createPackage();
        break;
    case 'update':
        requireRole('admin');
        updatePackage();
        break;
    case 'delete':
        requireRole('admin');
        deletePackage((int)($_GET['id'] ?? 0));
        break;
}

function getPackages() {
    global $conn;
    $result = $conn->query("SELECT * FROM packages WHERE status = 'active' ORDER BY created_at DESC");
    $packages = [];
    while ($row = $result->fetch_assoc()) {
        $packages[] = $row;
    }
    echo json_encode(['success' => true, 'data' => $packages]);
}

function getPackage($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM packages WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $pkg = $stmt->get_result()->fetch_assoc();
    if (!$pkg) { echo json_encode(['success' => false, 'message' => 'Package not found']); return; }

    $stmt2 = $conn->prepare("SELECT * FROM package_services WHERE package_id = ?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();
    $services = [];
    $res = $stmt2->get_result();
    while ($s = $res->fetch_assoc()) $services[] = $s;
    $pkg['services'] = $services;

    echo json_encode(['success' => true, 'data' => $pkg]);
}

function createPackage() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $stmt = $conn->prepare("INSERT INTO packages (name, category, description, venue, capacity, price, discount) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param("ssssidd", $data['name'], $data['category'], $data['description'], $data['venue'], $data['capacity'], $data['price'], $data['discount']);
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $conn->insert_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to create package']);
    }
}

function updatePackage() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $stmt = $conn->prepare("UPDATE packages SET name=?, category=?, description=?, venue=?, capacity=?, price=?, discount=?, status=? WHERE id=?");
    $stmt->bind_param("ssssiidsi", $data['name'], $data['category'], $data['description'], $data['venue'], $data['capacity'], $data['price'], $data['discount'], $data['status'], $data['id']);
    echo json_encode(['success' => $stmt->execute()]);
}

function deletePackage($id) {
    global $conn;
    $stmt = $conn->prepare("UPDATE packages SET status='inactive' WHERE id=?");
    $stmt->bind_param("i", $id);
    echo json_encode(['success' => $stmt->execute()]);
}
?>