<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Médico</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center text-primary">Regístrate como Profesional</h1>
        <p class="text-center">Únete a nuestra comunidad de médicos y ayuda a brindar bienestar.</p>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form action="procesar_registro.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="rol" value="medico">
                    
                    <div class="mb-3">
                        <label>Nombre Completo</label>
                        <input type="text" name="nombre" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Correo Electrónico</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Contraseña</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Especialidad</label>
                        <input type="text" name="especialidad" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Teléfono</label>
                        <input type="text" name="telefono" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label>Dirección</label>
                        <textarea name="direccion" class="form-control" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label>Certificado Profesional (PDF)</label>
                        <input type="file" name="certificado" class="form-control" accept="application/pdf" required>
                    </div>
                    <div class="mb-3">
                        <label>Foto de Perfil</label>
                        <input type="file" name="foto" class="form-control" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Registrar Médico</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
