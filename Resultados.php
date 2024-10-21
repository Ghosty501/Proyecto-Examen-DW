<?php
include 'components/sql.php';

// Función para convertir minutos a formato de horas:minutos
function convertirFormato($minutosTotales) {
    // Calcula las horas y los minutos
    $horas = floor($minutosTotales / 60); // Parte entera son las horas
    $minutos = $minutosTotales % 60;      // El resto son los minutos

    // Aseguramos que los minutos sean siempre dos dígitos
    $minutos = sprintf('%02d', $minutos);

    // Retornamos el formato de horas:minutos (ej. "3:00")
    return $horas . ':' . $minutos;
}

// Verificamos si se han enviado filtros a través del formulario
if (!empty($_POST)) {
    // Recuperamos los filtros del formulario (enviados por POST)
    $categoria = $_POST['categoria'] ?? [];
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $talent = $_POST['talent'] ?? [];
    $sede = $_POST['sede'] ?? [];

    // Construimos la consulta SQL base
    $query = "SELECT asesoria.ID, asesoria.Correo, asesoria.Fecha, asesoria.Duracion, categoria.Llave AS Categoria, asesor.Nombre AS Asesor
              FROM asesoria
              INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
              INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
              INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
              INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
              WHERE 1=1"; // Default condition to avoid syntax error

    // Aplicamos los filtros
    if (!empty($fechaInicio)) {
        $query .= " AND asesoria.Fecha >= STR_TO_DATE('" . $conn->real_escape_string($fechaInicio) . "', '%Y-%m-%d')";
    }
    if (!empty($fechaFin)) {
        $query .= " AND asesoria.Fecha <= STR_TO_DATE('" . $conn->real_escape_string($fechaFin) . "', '%Y-%m-%d')";
    }
    if (!empty($categoria) && is_array($categoria)) {
        $query .= " AND categoria.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $categoria)) . "')";
    }
    if (!empty($talent) && is_array($talent)) {
        $query .= " AND asesor.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $talent)) . "')";
    }
    if (!empty($sede) && is_array($sede)) {
        $query .= " AND sede.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $sede)) . "')";
    }

    // Ejecutamos la consulta y mostramos los resultados
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<table class = 'table table-hover table-dark'>
                <thead class='table-head'>
                    <tr>
                        <th>ID</th>
                        <th>Correo</th>
                        <th>Fecha</th>
                        <th>Duración</th>
                        <th>Categoría</th>
                        <th>Asesor</th>
                    </tr>
                </thead>
                <tbody class = 'table-group-divider'>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["ID"] . "</td>";
            echo "<td>" . $row["Correo"] . "</td>";
            echo "<td>" . $row["Fecha"] . "</td>";
            // Convertimos la duración de minutos a formato de horas:minutos
            echo "<td>" . convertirFormato($row["Duracion"]) . "</td>";
            echo "<td>" . $row["Categoria"] . "</td>";
            echo "<td>" . $row["Asesor"] . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";

    } else {
        echo "<tr><td colspan='6'>No se encontraron resultados</td></tr>";
    }

    $conn->close();
}
?>

<div id="resultado" class="mt-3 text-center"></div>
