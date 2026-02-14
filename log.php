<?php
// Log user submissions: IP, timestamp, text rows, background
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data || empty($data['text'])) {
    http_response_code(400);
    exit;
}

$entry = [
    'ip'        => $_SERVER['REMOTE_ADDR'] ?? '',
    'timestamp' => date('c'),
    'text'      => $data['text'],
    'bg'        => $data['bg'] ?? 'none',
];

$logFile = __DIR__ . '/out/submissions.jsonl';
file_put_contents($logFile, json_encode($entry, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND | LOCK_EX);

http_response_code(204);
