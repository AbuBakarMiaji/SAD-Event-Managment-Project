<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');
requireLogin();

$action = $_GET['action'] ?? 'list';

switch ($action) {
    case 'list':   listBookings();  break;
    case 'get':    getBooking((int)($_GET['id'] ?? 0)); break;
    case 'create': createBooking(); break;
    case 'update': updateBooking(); break;
    case 'cancel': cancelBooking((int)($_GET['id'] ?? 0)); break;
}

function listBookings() {
    global $conn;
    $user = currentUser();
    if ($user['role'] === 'customer') {
        $stmt = $conn->prepare("SELECT b.*, p.name AS package_name, p.category, u.name AS manager_name 
            FROM bookings b 
            JOIN packages p ON b.package_id = p.id 
            LEFT JOIN users u ON b.manager_id = u.id
            WHERE b.customer_id = ? ORDER BY b.created_at DESC");
        $stmt->bind_param("i", $user['id']);
    } elseif ($user['role'] === 'manager') {
        $stmt = $conn->prepare("SELECT b.*, p.name AS package_name, p.category, c.name AS customer_name 
            FROM bookings b 
            JOIN packages p ON b.package_id = p.id 
            JOIN users c ON b.customer_id = c.id
            WHERE b.manager_id = ? ORDER BY b.created_at DESC");
        $stmt->bind_param("i", $user['id']);
    } else {
        $stmt = $conn->prepare("SELECT b.*, p.name AS package_name, p.category, c.name AS customer_name, u.name AS manager_name 
            FROM bookings b 
            JOIN packages p ON b.package_id = p.id 
            JOIN users c ON b.customer_id = c.id
            LEFT JOIN users u ON b.manager_id = u.id
            ORDER BY b.created_at DESC");
    }
    $stmt->execute();
    $rows = [];
    $res = $stmt->get_result();
    while ($r = $res->fetch_assoc()) $rows[] = $r;
    echo json_encode(['success' => true, 'data' => $rows]);
}

function getBooking($id) {
    global $conn;
    $stmt = $conn->prepare("SELECT b.*, p.name AS package_name, c.name AS customer_name, c.email AS customer_email, c.phone AS customer_phone 
        FROM bookings b JOIN packages p ON b.package_id = p.id JOIN users c ON b.customer_id = c.id WHERE b.id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $booking = $stmt->get_result()->fetch_assoc();
    echo json_encode(['success' => true, 'data' => $booking]);
}

function createBooking() {
    global $conn;
    $user = currentUser();
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Calculate amount
    $stmt = $conn->prepare("SELECT price, discount FROM packages WHERE id = ?");
    $stmt->bind_param("i", $data['package_id']);
    $stmt->execute();
    $pkg = $stmt->get_result()->fetch_assoc();
    $total = $pkg['price'] * (1 - $pkg['discount'] / 100);

    $stmt = $conn->prepare("INSERT INTO bookings (customer_id, package_id, event_date, guest_count, special_requests, total_amount) VALUES (?,?,?,?,?,?)");
    $stmt->bind_param("iisisi", $user['id'], $data['package_id'], $data['event_date'], $data['guest_count'], $data['special_requests'], $total);
    if ($stmt->execute()) {
        $bookingId = $conn->insert_id;
        // Create pending payment
        $stmt2 = $conn->prepare("INSERT INTO payments (booking_id, amount) VALUES (?, ?)");
        $stmt2->bind_param("id", $bookingId, $total);
        $stmt2->execute();
        echo json_encode(['success' => true, 'id' => $bookingId, 'total' => $total]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Booking failed']);
    }
}

function updateBooking() {
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;
    $user = currentUser();

    if ($user['role'] === 'admin') {
        $stmt = $conn->prepare("UPDATE bookings SET status=?, manager_id=? WHERE id=?");
        $stmt->bind_param("sii", $data['status'], $data['manager_id'], $data['id']);
    } elseif ($user['role'] === 'manager') {
        $stmt = $conn->prepare("UPDATE bookings SET status=? WHERE id=? AND manager_id=?");
        $stmt->bind_param("sii", $data['status'], $data['id'], $user['id']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        return;
    }
    echo json_encode(['success' => $stmt->execute()]);
}

function cancelBooking($id) {
    global $conn;
    $user = currentUser();
    if ($user['role'] === 'customer') {
        $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND customer_id=? AND status='pending'");
        $stmt->bind_param("ii", $id, $user['id']);
    } else {
        $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=?");
        $stmt->bind_param("i", $id);
    }
    echo json_encode(['success' => $stmt->execute()]);
}
?>