<?php
require_once __DIR__ . '/../config.php';
header('Content-Type: application/json');

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    echo json_encode(['success' => false, 'error' => 'No payload']);
    exit;
}

$entity = $payload['entity_type'] ?? null;
$entityId = (int)($payload['entity_id'] ?? 0);
$reaction = $payload['reaction_type'] ?? null;

if (!$entity || $entityId <= 0 || !$reaction) {
    echo json_encode(['success' => false, 'error' => 'Invalid params']);
    exit;
}

try {
    $res = addReaction($pdo, $entity, $entityId, $reaction);
    echo json_encode(['success' => true, 'status' => $res['status']]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
