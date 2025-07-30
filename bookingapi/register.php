

<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: http://localhost:4200');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}


require 'connect.php'; // connects $con (MySQLi)

$input = json_decode(file_get_contents('php://input'), true);

$userName = trim($input['userName'] ?? '');
$password = trim($input['password'] ?? '');
$emailAddress = trim($input['emailAddress'] ?? '');

// Validation
if (!$userName || !$password || !$emailAddress) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit;
}

if (!filter_var($emailAddress, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit;
}

// Checking for a duplicate
$stmt = $con->prepare('SELECT COUNT(*) FROM users WHERE userName = ? OR emailAddress = ?');
$stmt->bind_param('ss', $userName, $emailAddress);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    echo json_encode(['success' => false, 'message' => 'User already exists']);
    exit;
}

// Hashing and insertion
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $con->prepare('INSERT INTO users (userName, password, emailAddress) VALUES (?, ?, ?)');
$stmt->bind_param('sss', $userName, $passwordHash, $emailAddress);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Registration successful']);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$con->close();

?>
