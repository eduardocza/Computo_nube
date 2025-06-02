<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario de Registro</title>
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --success-color: #4cc9f0;
            --error-color: #f72585;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #f5f7fa;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px 20px;
        }

        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 40px;
            width: 100%;
            max-width: 600px;
            margin-bottom: 40px;
        }

        .table-container {
            width: 100%;
            max-width: 1000px;
            overflow-x: auto;
            margin: 0 auto 40px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        h1, h2 {
            color: var(--primary-color);
            margin-bottom: 30px;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--dark-color);
            font-weight: 500;
        }

        input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 6px;
            font-size: 16px;
        }

        input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }

        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 14px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: var(--secondary-color);
        }

        .response {
            margin-top: 30px;
            padding: 20px;
            border-radius: 6px;
            background-color: #e8f4fd;
            border-left: 4px solid var(--success-color);
            display: none;
        }

        .error {
            border-left-color: var(--error-color) !important;
            background-color: #fef0f5 !important;
        }

        table {
            border-collapse: collapse;
            width: 100%;
            min-width: 600px;
            background: white;
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: var(--primary-color);
            color: white;
        }

        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Registro de Usuario</h1>
        <form method="post" action="">
            <div class="form-group">
                <label for="nombre">Nombre(s)</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>
            <div class="form-group">
                <label for="primer_apellido">Primer Apellido</label>
                <input type="text" id="primer_apellido" name="primer_apellido" required>
            </div>
            <div class="form-group">
                <label for="segundo_apellido">Segundo Apellido</label>
                <input type="text" id="segundo_apellido" name="segundo_apellido">
            </div>
            <div class="form-group">
                <label for="correo">Correo Electronico</label>
                <input type="email" id="correo" name="correo" required>
            </div>
            <div class="form-group">
                <label for="telefono">Telefono</label>
                <input type="tel" id="telefono" name="telefono" required>
            </div>
            <button type="submit" name="enviar" class="btn-submit">Enviar Datos</button>
        </form>

        <?php
        $host = "10.10.0.4"; // IP de tu servidor MySQL (Linux)
        $dbname = "proyecto";
        $username = "usuarioP";
        $password = "root";

        try {
            $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            // Crear tabla si no existe
            $conn->exec("CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(50) NOT NULL,
                primer_apellido VARCHAR(50) NOT NULL,
                segundo_apellido VARCHAR(50),
                correo VARCHAR(100) NOT NULL,
                telefono VARCHAR(20) NOT NULL,
                fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )");

            if (isset($_POST['enviar'])) {
                $stmt = $conn->prepare("INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, telefono)
                                        VALUES (:nombre, :primer_apellido, :segundo_apellido, :correo, :telefono)");
                $stmt->execute([
                    ':nombre' => $_POST['nombre'],
                    ':primer_apellido' => $_POST['primer_apellido'],
                    ':segundo_apellido' => $_POST['segundo_apellido'],
                    ':correo' => $_POST['correo'],
                    ':telefono' => $_POST['telefono']
                ]);

                echo '<div class="response">';
                echo '<h3>Datos guardados correctamente:</h3>';
                echo '<p><strong>Nombre:</strong> '.htmlspecialchars($_POST['nombre']).'</p>';
                echo '<p><strong>Primer Apellido:</strong> '.htmlspecialchars($_POST['primer_apellido']).'</p>';
                echo '<p><strong>Segundo Apellido:</strong> '.htmlspecialchars($_POST['segundo_apellido']).'</p>';
                echo '<p><strong>Correo:</strong> '.htmlspecialchars($_POST['correo']).'</p>';
                echo '<p><strong>Teléfono:</strong> '.htmlspecialchars($_POST['telefono']).'</p>';
                echo '</div>';
                echo '<script>document.querySelector(".response").style.display = "block";</script>';
            }
        } catch (PDOException $e) {
            echo '<div class="response error">';
            echo '<h3>Error de conexion o ejecucion:</h3>';
            echo '<p>'.htmlspecialchars($e->getMessage()).'</p>';
            echo '</div>';
            echo '<script>document.querySelector(".response").style.display = "block";</script>';
        }
        ?>
    </div>

    <?php
    try {
        if (isset($conn)) {
            // Mostrar datos en tabla
            $stmt = $conn->query("SELECT * FROM usuarios ORDER BY id DESC");

            echo '<div class="table-container">';
            echo '<h2>Usuarios Registrados</h2>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Nombre</th><th>Primer Apellido</th><th>Segundo Apellido</th><th>Correo</th><th>Teléfono</th><th>Fecha</th></tr>';
            while ($fila = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo '<tr>';
                echo '<td>'.$fila['id'].'</td>';
                echo '<td>'.htmlspecialchars($fila['nombre']).'</td>';
                echo '<td>'.htmlspecialchars($fila['primer_apellido']).'</td>';
                echo '<td>'.htmlspecialchars($fila['segundo_apellido']).'</td>';
                echo '<td>'.htmlspecialchars($fila['correo']).'</td>';
                echo '<td>'.htmlspecialchars($fila['telefono']).'</td>';
                echo '<td>'.$fila['fecha_registro'].'</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '</div>';
        }
    } catch (PDOException $e) {
        echo '<div class="table-container">';
        echo '<div class="response error">';
        echo '<h3>Error al cargar los datos:</h3>';
        echo '<p>'.htmlspecialchars($e->getMessage()).'</p>';
        echo '</div>';
        echo '</div>';
    }
    ?>
</body>
</html>
