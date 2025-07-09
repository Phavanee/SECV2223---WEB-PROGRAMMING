<?php
header('Content-Type: application/json');
include_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);
$user_type = $data['user_type'] ?? '';
$name = $data['name'] ?? '';
$email = $data['email'] ?? '';
$password = $data['password'] ?? '';
$phone = $data['phone'] ?? '';
$location = $data['location'] ?? '';
$gender = $data['gender'] ?? '';

if (!$user_type || !$name || !$email || !$password || !$phone || !$location || !$gender) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

$table = $user_type === 'student' ? 'users' : ($user_type === 'employer' ? 'employers' : '');
if (!$table) {
    echo json_encode(['success' => false, 'message' => 'Invalid user type.']);
    exit;
}

// Check for duplicate email
$stmt = $conn->prepare("SELECT id FROM $table WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already registered.']);
    exit;
}
$stmt->close();

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$stmt = $conn->prepare("INSERT INTO $table (name, email, password, phone_number, location, gender) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('ssssss', $name, $email, $hashed_password, $phone, $location, $gender);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful! You can now log in.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Registration failed.']);
}
$stmt->close();
?> 