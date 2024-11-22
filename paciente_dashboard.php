<?php
session_start();
require_once 'conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

// Obtener los datos del paciente logueado
try {
    $stmt = $pdo->prepare('SELECT * FROM pacientes WHERE id = ?');
    $stmt->execute([$_SESSION['usuario_id']]);
    $paciente = $stmt->fetch();

    if (!$paciente) {
        // Si no se encuentra el paciente, redirigir a la página de inicio
        header('Location: index.php');
        exit;
    }
} catch (PDOException $e) {
    die('Error al cargar el perfil: ' . $e->getMessage());
}

// Procesar la subida de la foto de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto_perfil'])) {
    $foto = $_FILES['foto_perfil'];
    if ($foto['error'] === UPLOAD_ERR_OK) {
        $nombreArchivo = uniqid() . "_" . basename($foto['name']);
        $rutaDestino = "uploads/" . $nombreArchivo;
        
        if (move_uploaded_file($foto['tmp_name'], $rutaDestino)) {
            // Actualizar la ruta de la foto en la base de datos
            $stmt = $pdo->prepare('UPDATE pacientes SET foto = ? WHERE id = ?');
            $stmt->execute([$nombreArchivo, $_SESSION['usuario_id']]);
            header('Location: paciente_dashboard.php'); // Recargar la página
            exit;
        } else {
            $error = "Error al subir la foto.";
        }
    } else {
        $error = "No se pudo procesar la subida de la foto.";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Paciente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f9fafb;
            font-family: 'Inter', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .navbar {
            background-color: #2563eb;
            color: white;
            padding: 1rem;
        }

        .navbar h1 {
            font-size: 1.5rem;
            margin: 0;
        }

        .container {
            margin-top: 20px;
        }

        .profile-header {
            text-align: center;
            padding: 2rem;
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .profile-picture {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
            margin: 0 auto 1rem;
            border: 3px solid #2563eb;
        }

        .profile-picture img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .profile-header h2 {
            font-size: 1.5rem;
            color: #1f2937;
        }

        .profile-header p {
            font-size: 1rem;
            color: #6b7280;
        }

        .actions {
            margin-top: 2rem;
            text-align: center;
        }

        .actions button,
        .actions a {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            margin: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .actions button:hover,
        .actions a:hover {
            background-color: #1e40af;
        }

        .motivational-message {
            margin-top: 2rem;
            text-align: center;
            padding: 2rem;
            background: #eff6ff;
            border-radius: 1rem;
            border: 1px solid #bfdbfe;
        }

        .motivational-message h3 {
            color: #2563eb;
            margin-bottom: 1rem;
        }

        .motivational-message p {
            color: #1f2937;
        }

        footer {
            margin-top: auto;
            background: #1f2937;
            color: white;
            text-align: center;
            padding: 1rem;
        }

        .upload-form {
            text-align: center;
            margin-top: 2rem;
        }

        .upload-form input[type="file"] {
            display: none;
        }

        .upload-label {
            background-color: #2563eb;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            cursor: pointer;
            display: inline-block;
            transition: all 0.3s ease;
        }

        .upload-label:hover {
            background-color: #1e40af;
        }

        .error-message {
            margin-top: 1rem;
            color: #dc2626;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <h1>Bienvenido, <?php echo htmlspecialchars($paciente['nombre']); ?></h1>
    </nav>

    <div class="container">
        <div class="profile-header">
            <div class="profile-picture">
                <?php if (!empty($paciente['foto'])): ?>
                    <img src="uploads/<?php echo htmlspecialchars($paciente['foto']); ?>" alt="Foto de perfil">
                <?php else: ?>
                    <img src="https://via.placeholder.com/120" alt="Foto de perfil por defecto">
                <?php endif; ?>
            </div>
            <h2><?php echo htmlspecialchars($paciente['nombre']); ?></h2>
            <p><?php echo htmlspecialchars($paciente['email']); ?></p>
        </div>

        <div class="upload-form">
            <form method="POST" enctype="multipart/form-data">
                <label class="upload-label" for="foto_perfil">
                    <i class="fas fa-upload"></i> Subir Foto
                </label>
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*">
                <button type="submit">Actualizar</button>
            </form>
            <?php if (isset($error)): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
        </div>

        <div class="motivational-message">
            <h3>¡Estamos aquí para ayudarte!</h3>
            <p>Visita nuestro listado de médicos y agenda una cita fácilmente.</p>
            <a href="medicos.php">Ver Médicos</a>
        </div>
    </div>

    <footer>
        &copy; <?php echo date('Y'); ?> Sistema Médico. Todos los derechos reservados.
    </footer>
</body>
</html>
