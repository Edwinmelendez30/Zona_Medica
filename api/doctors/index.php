<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    try {
        $stmt = $pdo->query('SELECT id, nombre, email, profesion, ubicacion, telefono, foto, calificacion FROM medicos');
        $doctors = $stmt->fetchAll();
        
        if (empty($doctors)) {
            echo json_encode([]);
        } else {
            echo json_encode($doctors);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error fetching doctors: ' . $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
}