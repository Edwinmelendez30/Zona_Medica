<?php
require_once 'config.php';

try {
    // Validar los datos recibidos
    $medico_id = $_POST['medico_id'] ?? null;
    $fecha = $_POST['fecha_cita'] ?? null;
    $horario = $_POST['horario_cita'] ?? null;
    $paciente_nombre = $_POST['paciente_nombre'] ?? null;
    $paciente_email = $_POST['paciente_email'] ?? null;
    $paciente_telefono = $_POST['paciente_telefono'] ?? null;
    $motivo = $_POST['motivo'] ?? null;

    if (!$medico_id || !$fecha || !$horario || !$paciente_nombre || !$paciente_email || !$paciente_telefono) {
        throw new Exception('Todos los campos son requeridos');
    }

    // Separar el horario en hora inicio y fin
    [$hora_inicio, $hora_fin] = explode('-', $horario);

    // Verificar si el horario sigue disponible
    $stmt = $pdo->prepare("
        SELECT COUNT(*) 
        FROM citas 
        WHERE medico_id = :medico_id 
        AND fecha = :fecha 
        AND estado != 'Cancelada'
        AND (
            (hora_inicio <= :hora_inicio AND hora_fin > :hora_inicio)
            OR (hora_inicio < :hora_fin AND hora_fin >= :hora_fin)
            OR (hora_inicio >= :hora_inicio AND hora_fin <= :hora_fin)
        )
    ");

    $stmt->execute([
        ':medico_id' => $medico_id,
        ':fecha' => $fecha,
        ':hora_inicio' => $hora_inicio,
        ':hora_fin' => $hora_fin
    ]);

    if ($stmt->fetchColumn() > 0) {
        throw new Exception('El horario seleccionado ya no está disponible');
    }

    // Iniciar transacción
    $pdo->beginTransaction();

    // Guardar la cita
    $stmt = $pdo->prepare("
        INSERT INTO citas (
            medico_id, 
            fecha, 
            hora_inicio, 
            hora_fin, 
            paciente_nombre, 
            paciente_email, 
            paciente_telefono, 
            motivo,
            estado
        ) VALUES (
            :medico_id,
            :fecha,
            :hora_inicio,
            :hora_fin,
            :paciente_nombre,
            :paciente_email,
            :paciente_telefono,
            :motivo,
            'Pendiente'
        )
    ");

    $stmt->execute([
        ':medico_id' => $medico_id,
        ':fecha' => $fecha,
        ':hora_inicio' => $hora_inicio,
        ':hora_fin' => $hora_fin,
        ':paciente_nombre' => $paciente_nombre,
        ':paciente_email' => $paciente_email,
        ':paciente_telefono' => $paciente_telefono,
        ':motivo' => $motivo
    ]);

    $cita_id = $pdo->lastInsertId();

    // Obtener información del médico para el correo
    $stmt = $pdo->prepare("SELECT nombre, email FROM medicos WHERE id = :medico_id");
    $stmt->execute([':medico_id' => $medico_id]);
    $medico = $stmt->fetch(PDO::FETCH_ASSOC);

    // Enviar correo al paciente
    $to = $paciente_email;
    $subject = "Confirmación de Cita Médica";
    $message = "
    <html>
    <head>
        <title>Confirmación de Cita Médica</title>
    </head>
    <body>
        <h2>Confirmación de Cita Médica</h2>
        <p>Estimado/a {$paciente_nombre},</p>
        <p>Su cita ha sido programada exitosamente:</p>
        <ul>
            <li><strong>Médico:</strong> {$medico['nombre']}</li>
            <li><strong>Fecha:</strong> {$fecha}</li>
            <li><strong>Hora:</strong> {$hora_inicio}</li>
        </ul>
        <p>Por favor, llegue 15 minutos antes de su cita.</p>
        <p>Si necesita cancelar o reprogramar su cita, por favor contáctenos con al menos 24 horas de anticipación.</p>
    </body>
    </html>
    ";

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: clinica@example.com" . "\r\n";

    mail($to, $subject, $message, $headers);

    // Confirmar transacción
    $pdo->commit();

    // Devolver respuesta exitosa
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'message' => 'Cita agendada exitosamente',
        'cita_id' => $cita_id
    ]);

} catch (Exception $e) {
    // Revertir transacción en caso de error
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}