<?php include('conexion.php'); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Asistencia Escolar</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <div class="container">
        <h1>📊 Panel de Asistencia</h1>
        
        <!-- Resumen General -->
        <div class="resumen">
            <h2>Resumen Mensual</h2>
            <?php
            $query = "SELECT COUNT(*) as total, 
                             SUM(asistencia = 'presente') as presentes,
                             SUM(asistencia = 'falta') as faltas
                      FROM asistencias";
            $result = $conexion->query($query);
            $row = $result->fetch_assoc();
            $porcentaje = round(($row['presentes'] / $row['total']) * 100, 2);
            ?>
            <p>Asistencia: <strong><?php echo $porcentaje; ?>%</strong></p>
            <p>Faltas: <?php echo $row['faltas']; ?></p>
        </div>

        <!-- Tabla de Asistencias -->
        <table>
            <tr>
                <th>Alumno</th>
                <th>Grado</th>
                <th>Fecha</th>
                <th>Asistencia</th>
                <th>Justificación</th>
            </tr>
            <?php
            $query = "SELECT * FROM asistencias ORDER BY fecha DESC";
            $result = $conexion->query($query);
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['alumno_nombre']}</td>
                        <td>{$row['grado']}</td>
                        <td>{$row['fecha']}</td>
                        <td class='{$row['asistencia']}'>{$row['asistencia']}</td>
                        <td>{$row['justificacion'] ?? '--'}</td>
                      </tr>";
            }
            ?>
        </table>

        <!-- Alumnos con más faltas -->
        <div class="alertas">
            <h2>🚨 Alertas: Faltas Recurrentes</h2>
            <?php
            $query = "SELECT alumno_nombre, COUNT(*) as faltas 
                      FROM asistencias 
                      WHERE asistencia = 'falta' 
                      GROUP BY alumno_nombre 
                      HAVING faltas >= 2 
                      ORDER BY faltas DESC";
            $result = $conexion->query($query);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<p>{$row['alumno_nombre']} - {$row['faltas']} faltas</p>";
                }
            } else {
                echo "<p>No hay alertas.</p>";
            }
            ?>
        </div>
    </div>

</body>
</html>