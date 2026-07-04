<?php
/**
 * Contact Form API — handles AJAX submissions
 */
header('Content-Type: application/json');

require_once __DIR__ . '/../functions.php';
require_once __DIR__ . '/../db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;

if (!verify_csrf_token($input['csrf_token'] ?? '')) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => t('Security check failed. Please refresh.', 'پشکنینی ئاسایش سەرکەوتوو نەبوو. تکایە نوێ بکەرەوە.')]);
    exit;
}

$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$message = trim($input['message'] ?? '');

if ($name === '' || $email === '' || $message === '') {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => t('Please fill in all fields.', 'تکایە هەموو خانەکان پڕ بکەرەوە.'),
        'csrf_token' => csrf_token()
    ]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => t('Please enter a valid email.', 'تکایە ئیمەیڵێکی دروست بنووسە.'),
        'csrf_token' => csrf_token()
    ]);
    exit;
}

if (mb_strlen($message) > 5000) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => t('Message is too long.', 'پەیامەکە زۆر درێژە.'),
        'csrf_token' => csrf_token()
    ]);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->execute([$name, $email, $message]);

    echo json_encode([
        'success' => true,
        'message' => t('Thank you! Your message has been sent.', 'سوپاس! پەیامەکەت نێردرا.'),
        'csrf_token' => csrf_token()
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => t('Something went wrong. Please try again.', 'هەڵەیەک ڕوویدا. تکایە دووبارە هەوڵ بدەرەوە.'),
        'csrf_token' => csrf_token()
    ]);
}
