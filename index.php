<?php
// --- Conexión a SQL Server ---
$servidor = "tcp:formdb.database.windows.net,1433";
$opciones = array(
    "Database" => "proy",
    "UID" => "lalon",
    "PWD" => "Lalopass17062004@",
    "CharacterSet" => "UTF-8"
);

$conexion = sqlsrv_connect($servidor, $opciones);

if ($conexion === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Crear tabla si no existe
$crearTablaSQL = "
IF NOT EXISTS (SELECT * FROM sysobjects WHERE name='usuarios' AND xtype='U')
CREATE TABLE usuarios (
    id INT IDENTITY(1,1) PRIMARY KEY,
    nombre NVARCHAR(50) NOT NULL,
    primer_apellido NVARCHAR(50) NOT NULL,
    segundo_apellido NVARCHAR(50),
    correo NVARCHAR(100) NOT NULL,
    telefono NVARCHAR(20) NOT NULL,
    fecha_registro DATETIME DEFAULT GETDATE()
)";
sqlsrv_query($conexion, $crearTablaSQL);

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['enviar'])) {
    $nombre = $_POST['nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $correo = $_POST['correo'];
    $telefono = $_POST['telefono'];

    if (!empty($nombre) && !empty($primer_apellido) && !empty($correo) && !empty($telefono)) {
        $sqlInsert = "INSERT INTO usuarios (nombre, primer_apellido, segundo_apellido, correo, telefono) 
                      VALUES (?, ?, ?, ?, ?)";
        $params = array($nombre, $primer_apellido, $segundo_apellido, $correo, $telefono);
        $stmt = sqlsrv_query($conexion, $sqlInsert, $params);

        if ($stmt === false) {
            $mensajeError = print_r(sqlsrv_errors(), true);
            $esError = true;
        } else {
            $esError = false;
        }
    }
}
?>
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

        <?php if (isset($esError)): ?>
            <div class="response <?php echo $esError ? 'error' : ''; ?>" style="display: block;">
                <?php if ($esError): ?>
                    <h3>Error al guardar:</h3>
                    <p><?php echo htmlspecialchars($mensajeError); ?></p>
                <?php else: ?>
                    <h3>Datos guardados correctamente:</h3>
                    <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
                    <p><strong>Primer Apellido:</strong> <?php echo htmlspecialchars($primer_apellido); ?></p>
                    <p><strong>Segundo Apellido:</strong> <?php echo htmlspecialchars($segundo_apellido); ?></p>
                    <p><strong>Correo:</strong> <?php echo htmlspecialchars($correo); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($telefono); ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
    $consultaSQL = "SELECT * FROM usuarios ORDER BY id DESC";
    $consulta = sqlsrv_query($conexion, $consultaSQL);
    ?>

    <div class="table-container">
        <h2>Usuarios Registrados</h2>
        <table>
            <tr><th>ID</th><th>Nombre</th><th>Primer Apellido</th><th>Segundo Apellido</th><th>Correo</th><th>Teléfono</th><th>Fecha</th></tr>
            <?php
            if ($consulta === false) {
                echo "<tr><td colspan='7'>Error al consultar.</td></tr>";
            } else {
                $hayDatos = false;
                while ($fila = sqlsrv_fetch_array($consulta, SQLSRV_FETCH_ASSOC)) {
                    $hayDatos = true;
                    echo "<tr>
                        <td>{$fila['id']}</td>
                        <td>".htmlspecialchars($fila['nombre'])."</td>
                        <td>".htmlspecialchars($fila['primer_apellido'])."</td>
                        <td>".htmlspecialchars($fila['segundo_apellido'])."</td>
                        <td>".htmlspecialchars($fila['correo'])."</td>
                        <td>".htmlspecialchars($fila['telefono'])."</td>
                        <td>".$fila['fecha_registro']->format('Y-m-d H:i:s')."</td>
                    </tr>";
                }

                if (!$hayDatos) {
                    echo "<tr><td colspan='7'>No hay registros.</td></tr>";
                }
            }

            sqlsrv_close($conexion);
            ?>
        </table>
    </div>
</body>
</html>
