<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['medico_id'], $data['fecha'], $data['hora_inicio'], $data['motivo_consulta'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required fields']);
        exit;
    }

    try {
        // Calculate hora_fin based on the doctor's appointment interval
        $stmt = $pdo->prepare('
            SELECT intervalo_citas 
            FROM horarios_medicos 
            WHERE medico_id = ? AND dia_semana = DAYNAME(?)
        ');
        $stmt->execute([$data['medico_id'], $data['fecha']]);
        $schedule = $stmt->fetch();
        
        if (!$schedule) {
            http_response_code(400);
            echo json_encode(['error' => 'No schedule available for this day']);
            exit;
        }

        $hora_inicio = strtotime($data['hora_inicio']);
        $hora_fin = date('H:i:s', $hora_inicio + ($schedule['intervalo_citas'] * 60));

        // Insert the appointment
        $stmt = $pdo->prepare('
            INSERT INTO citas (
                paciente_id, 
                medico_id, 
                fecha, 
                hora_inicio, 
                hora_fin, 
                motivo_consulta, 
                estado
            ) VALUES (
                1, 
                ?, 
                ?, 
                ?, 
                ?, 
                ?, 
                "Pendiente"
            )
        ');

        $stmt->execute([
            $data['medico_id'],
            $data['fecha'],
            $data['hora_inicio'],
            $hora_fin,
            $data['motivo_consulta']
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Appointment scheduled successfully',
            'id' => $pdo->lastInsertId()
        ]);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error creating appointment: ' . $e->getMessage()]);
    }
}