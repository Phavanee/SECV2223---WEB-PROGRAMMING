<?php
session_start();
header('Content-Type: application/json');
include_once 'db.php';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Email and password are required.']);
    exit;
}

$user = null;
$userType = null;

// Check admins
$stmt = $conn->prepare('SELECT id, name, email, password FROM admins WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();
    $userType = 'admin';
}
$stmt->close();

// Check employers if not found in admins
if (!$user) {
    $stmt = $conn->prepare('SELECT id, name, email, password FROM employers WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $userType = 'employer';
    }
    $stmt->close();
}

// Check users if not found in admins or employers
if (!$user) {
    $stmt = $conn->prepare('SELECT id, name, email, password FROM users WHERE email = ? LIMIT 1');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        $userType = 'student';
    }
    $stmt->close();
}

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_type'] = $userType;
    $_SESSION['user_name'] = $user['name'];
    echo json_encode(['success' => true, 'user_type' => $userType, 'user_id' => $user['id'], 'user_name' => $user['name']]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid email or password.']);
}
?> 