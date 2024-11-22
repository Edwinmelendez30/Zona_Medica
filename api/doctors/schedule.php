<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $doctor_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if (!$doctor_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Doctor ID is required']);
        exit;
    }

    try {
        $stmt = $pdo->prepare('SELECT * FROM horarios_medicos WHERE medico_id = ? AND estado = 1');
        $stmt->execute([$doctor_id]);
        $schedule = $stmt->fetchAll();
        echo json_encode($schedule);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching schedule: ' . $e->getMessage()]);
    }
}