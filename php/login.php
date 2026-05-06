<?php
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required']);
    exit;
}

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ? AND status = 'active'");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];

    echo json_encode([
        'success'  => true,
        'role'     => $user['role'],
        'name'     => $user['name'],
        'redirect' => getDashboard($user['role'])
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password']);
}

function getDashboard($role) {
    $map = [
        'admin'    => 'pages/admin-dashboard.html',
        'customer' => 'pages/customer-dashboard.html',
        'manager'  => 'pages/manager-dashboard.html',
        'staff'    => 'pages/staff-dashboard.html',
    ];
    return $map[$role] ?? 'index.html';
}
?>