<?php
require_once '../config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $doctor_id = isset($data['doctor_id']) ? (int)$data['doctor_id'] : 0;
    $date = isset($data['date']) ? $data['date'] : '';
    $schedule_id = isset($data['scheduleId']) ? (int)$data['scheduleId'] : 0;

    if (!$doctor_id || !$date || !$schedule_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing required parameters']);
        exit;
    }

    try {
        // Get the schedule for the given day
        $stmt = $pdo->prepare('
            SELECT hora_inicio, hora_fin, intervalo_citas 
            FROM horarios_medicos 
            WHERE id = ? AND medico_id = ? AND estado = 1
        ');
        $stmt->execute([$schedule_id, $doctor_id]);
        $schedule = $stmt->fetch();

        if (!$schedule) {
            echo json_encode([]);
            exit;
        }

        // Get existing appointments for the day
        $stmt = $pdo->prepare('
            SELECT hora_inicio 
            FROM citas 
            WHERE medico_id = ? AND fecha = ? AND estado != "Cancelada"
        ');
        $stmt->execute([$doctor_id, $date]);
        $booked_times = $stmt->fetchAll(PDO::FETCH_COLUMN);

        // Generate available time slots
        $available_times = [];
        $current_time = strtotime($schedule['hora_inicio']);
        $end_time = strtotime($schedule['hora_fin']);
        $interval = $schedule['intervalo_citas'] * 60; // Convert to seconds

        while ($current_time < $end_time) {
            $time_slot = date('H:i:s', $current_time);
            if (!in_array($time_slot, $booked_times)) {
                $available_times[] = $time_slot;
            }
            $current_time += $interval;
        }

        echo json_encode($available_times);
    } catch (PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Error generating available times: ' . $e->getMessage()]);
    }
}