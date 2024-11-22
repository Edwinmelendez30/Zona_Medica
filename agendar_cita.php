<?php
require_once 'conexion.php';
session_start();

// Verificar si el usuario es paciente
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'paciente') {
    header('Location: login.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agendar Cita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h3>Agendar Cita</h3>
    <!-- Formulario para agendar -->
</div>
</body>
</html>
