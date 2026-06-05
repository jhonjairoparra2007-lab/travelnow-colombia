<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TravelNow Colombia S.A.S. - Reservas de Hoteles</title>
    
    <!-- Enlace a estilos CSS sencillos y limpios -->
    <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
</head>
<body>

    <!-- Parte 1: Barra superior empresarial simple -->
    <div class="navbar">
        <h1>TravelNow Colombia S.A.S.</h1>
        <p>Sistema de Reservas y Gestión Hotelera</p>
    </div>

    <div class="container">
        
        <!-- Alertas de Éxito al Registrar Reserva (Parte 3 - POST Form response) -->
        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <strong><?php echo e(session('success')); ?></strong>
                <p style="margin: 5px 0;">La reserva se ha enviado por método POST a la API REST externa correctamente.</p>
                
                <div style="background-color: white; padding: 10px; border-radius: 4px; margin-top: 10px; color: #1f2937; border: 1px solid #34d399;">
                    <strong>Datos Enviados:</strong><br>
                    - <strong>Título (title):</strong> <?php echo e(session('sent_title')); ?><br>
                    - <strong>Cuerpo (body):</strong> <?php echo e(session('sent_body')); ?>

                </div>

                <!-- Consumir datos JSON devueltos por el servidor POST -->
                <div style="margin-top: 10px;">
                    <strong>Respuesta JSON del Servidor:</strong>
                    <pre class="json-container"><?php echo e(json_encode(session('response_json'), JSON_PRETTY_PRINT)); ?></pre>
                </div>
            </div>
        <?php endif; ?>

        <!-- Alertas de Error y Validaciones -->
        <?php if($errors->any()): ?>
            <div class="alert alert-danger">
                <strong>Ocurrieron errores al procesar el formulario:</strong>
                <ul>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li><?php echo e($error); ?></li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Layout en 2 Columnas -->
        <div class="main-layout">
            
            <!-- Columna Izquierda: Tabla de Hoteles -->
            <div class="card">
                <h2>Catálogo de Hoteles Disponibles</h2>
                <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px;">
                    Los siguientes datos son consultados en tiempo real desde la API REST y enriquecidos en nuestro servidor:
                </p>

                <div class="table-responsive">
                    <!-- Tabla Empresarial Moderna con Nuevos Datos (Parte 1 y 2) -->
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Imagen</th>
                                <th>Nombre del Hotel / Descripción</th>
                                <th>Ciudad</th>
                                <th>Precio / Noche</th>
                                <th>Habitaciones</th>
                                <th>Calificación</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $hotels; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hotel): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr>
                                    <td><strong>#<?php echo e($hotel['id']); ?></strong></td>
                                    
                                    <!-- Parte 1 - Imagen del hotel -->
                                    <td>
                                        <img src="<?php echo e($hotel['image']); ?>" alt="Hotel" class="hotel-img">
                                    </td>
                                    
                                    <td>
                                        <span style="font-weight: bold; color: #1e3a8a; display: block;"><?php echo e($hotel['name']); ?></span>
                                        <span style="font-size: 12px; color: #4b5563; display: block; max-width: 250px;"><?php echo e($hotel['description']); ?></span>
                                    </td>
                                    
                                    <!-- Parte 2 - Ciudad -->
                                    <td>
                                        <span class="badge badge-city"><?php echo e($hotel['city']); ?></span>
                                    </td>
                                    
                                    <!-- Parte 2 - Precio -->
                                    <td>
                                        <span class="price">$<?php echo e(number_format($hotel['price'], 0, ',', '.')); ?></span>
                                    </td>
                                    
                                    <!-- Parte 2 - Cantidad de habitaciones -->
                                    <td>
                                        <span class="badge <?php echo e($hotel['rooms'] > 5 ? 'badge-rooms' : 'badge-critical'); ?>">
                                            <?php echo e($hotel['rooms']); ?> disponibles
                                        </span>
                                    </td>
                                    
                                    <!-- Parte 2 - Calificación -->
                                    <td>
                                        <span style="color: #f59e0b; font-weight: bold;">⭐ <?php echo e(number_format($hotel['rating'], 1)); ?></span>
                                    </td>
                                    
                                    <td>
                                        <!-- Botón para seleccionar y rellenar automáticamente el formulario -->
                                        <button type="button" class="btn btn-sm" onclick="seleccionarHotel('<?php echo e($hotel['name']); ?>', '<?php echo e($hotel['city']); ?>')">
                                            Seleccionar
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 20px;">No hay hoteles disponibles.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Columna Derecha: Formulario POST (Parte 3) -->
            <div class="card" id="form-section">
                <h2>Registrar Reserva</h2>
                <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px;">
                    Usa este formulario para registrar una nueva reserva turística mediante una petición POST a la API:
                </p>

                <form action="<?php echo e(route('hotels.reserve')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    
                    <!-- Campo Título (title) -->
                    <div class="form-group">
                        <label for="title_input">Título de la Reserva (Hotel)</label>
                        <input type="text" class="form-control" id="title_input" name="title" value="<?php echo e(old('title', 'Reserva Hotel Cartagena')); ?>" placeholder="Ej. Reserva Hotel Cartagena" required>
                    </div>

                    <!-- Campo Detalles (body) -->
                    <div class="form-group">
                        <label for="body_input">Detalles de la Reserva (Personas / Info)</label>
                        <input type="text" class="form-control" id="body_input" name="body" value="<?php echo e(old('body', 'Reserva para 2 personas')); ?>" placeholder="Ej. Reserva para 2 personas" required>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Enviar Reserva por POST
                    </button>
                </form>
            </div>

        </div>
    </div>

    <!-- Marca de agua flotante en la esquina -->
    <div style="position: fixed; bottom: 15px; right: 15px; opacity: 0.65; background-color: #ffffff; border: 1px solid #cbd5e1; padding: 6px 12px; border-radius: 4px; font-size: 11px; font-weight: bold; color: #1e3a8a; z-index: 1000; pointer-events: none; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
        ADSO 35 SENA - Jhon Jairo Parra Obando
    </div>

    <footer>
        <p>&copy; <?php echo e(date('Y')); ?> TravelNow Colombia S.A.S. - Sistema de Reservas Escolares - Laravel MVC</p>
        <p style="margin-top: 8px; font-weight: bold; color: #1e3a8a; font-size: 13px;">
            Creado por Jhon Jairo Parra Obando del ADSO 35 SENA
        </p>
    </footer>

    <!-- Script simple para facilitar la selección de hoteles en la tabla -->
    <script>
        function seleccionarHotel(nombre, ciudad) {
            // Modificar los valores del formulario al hacer click en "Seleccionar" en la tabla
            document.getElementById('title_input').value = 'Reserva ' + nombre + ' (' + ciudad + ')';
            document.getElementById('body_input').value = 'Reserva para 2 personas';
            
            // Hacer scroll hasta el formulario
            document.getElementById('form-section').scrollIntoView({ behavior: 'smooth' });
        }
    </script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\Laravel\travelnow-colombia\resources\views/hotels.blade.php ENDPATH**/ ?>