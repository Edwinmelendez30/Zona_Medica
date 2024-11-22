<?php
include 'conexion.php';  

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $query = "SELECT * FROM medicos WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $medico = $result->fetch_assoc();

    if (!$medico) {
        echo "Médico no encontrado";
        exit;
    }
} else {
    echo "ID de médico no proporcionado";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?php echo $medico['nombre']; ?> | Zona Médica</title>
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
    
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --dark-gray: #343a40;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-gray);
        }

        .profile-container {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .profile-header {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
            text-align: center;
        }

        .profile-photo {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            border: 4px solid var(--secondary-color);
            margin: 0 auto 1.5rem;
            object-fit: cover;
        }

        .doctor-name {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }

        .doctor-specialty {
            color: var(--secondary-color);
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .rating-display {
            color: #ffc107;
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .profile-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .info-item {
            padding: 1rem;
            border-radius: 10px;
            background: var(--light-gray);
        }

        .info-item i {
            color: var(--secondary-color);
            margin-right: 0.5rem;
        }

        .description-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-top: 2rem;
        }

        .btn-action {
            padding: 1rem 2rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .btn-primary-action {
            background-color: var(--secondary-color);
            color: white;
            border: none;
        }

        .btn-primary-action:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            color: white;
            text-decoration: none;
        }

        .btn-secondary-action {
            background-color: white;
            color: var(--secondary-color);
            border: 2px solid var(--secondary-color);
        }

        .btn-secondary-action:hover {
            background-color: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }

        .reviews-section {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .profile-photo {
                width: 150px;
                height: 150px;
            }

            .doctor-name {
                font-size: 2rem;
            }

            .info-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="site-header">
        <div class="container text-center">
            <h1 class="animate__animated animate__fadeInDown">Zona Médica</h1>
            <p class="text-white mt-2 animate__animated animate__fadeInUp">Perfil Médico</p>
        </div>
    </header>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg custom-navbar sticky-top">
        <div class="container">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">
                            <i class="fas fa-home"></i> Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="medicos.php">
                            <i class="fas fa-arrow-left"></i> Volver a Médicos
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="profile-container">
        <!-- Perfil Header -->
        <div class="profile-header animate__animated animate__fadeIn">
            <img src="<?php echo $medico['foto']; ?>" alt="<?php echo $medico['nombre']; ?>" class="profile-photo">
            <h1 class="doctor-name"><?php echo $medico['nombre']; ?></h1>
            <div class="doctor-specialty">
                <i class="fas fa-stethoscope"></i> <?php echo $medico['profesion']; ?>
            </div>
            <div class="rating-display">
                <?php for($i = 0; $i < $medico['calificacion']; $i++): ?>
                    <i class="fas fa-star"></i>
                <?php endfor; ?>
                <span class="ml-2"><?php echo $medico['calificacion']; ?>.0</span>
            </div>
        </div>

        <!-- Información Principal -->
        <div class="profile-section animate__animated animate__fadeIn">
            <h2 class="mb-4"><i class="fas fa-info-circle"></i> Información del Médico</h2>
            <div class="info-grid">
                <div class="info-item">
                    <i class="fas fa-envelope"></i>
                    <strong>Correo:</strong><br>
                    <?php echo $medico['email']; ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-id-card"></i>
                    <strong>Cédula Profesional:</strong><br>
                    <?php echo $medico['cedula']; ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-phone"></i>
                    <strong>Teléfono:</strong><br>
                    <?php echo $medico['telefono']; ?>
                </div>
                <div class="info-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <strong>Ubicación:</strong><br>
                    <?php echo $medico['ubicacion']; ?>
                </div>
            </div>
        </div>

        <!-- Descripción -->
        <div class="description-section animate__animated animate__fadeIn">
            <h2 class="mb-4"><i class="fas fa-user-md"></i> Acerca del Doctor</h2>
            <p><?php echo $medico['descripcion'] ? $medico['descripcion'] : "No hay descripción disponible."; ?></p>
        </div>

        <div class="action-buttons">
            <a href="login.php?redirect=agendar_cita.php?id=<?php echo $medico['id']; ?>" class="btn-action btn-primary-action">
                <i class="fas fa-calendar-alt"></i> Agendar Cita
    </a>
      <a href="login.php?redirect=contactar.php?id=<?php echo $medico['id']; ?>" class="btn-action btn-secondary-action">
                <i class="fas fa-comment"></i> Contactar
            </a>
     <button onclick="dejarResena()" class="btn-action btn-secondary-action">
                <i class="fas fa-star"></i> Dejar Reseña
            </button>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function dejarResena() {
            
            alert('Próximamente: Sistema de reseñas');
        }
    </script>
</body>
</html>