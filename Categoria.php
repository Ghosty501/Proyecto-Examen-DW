<?php
include 'components/sql.php';

// Función para convertir minutos a formato de horas:minutos
function convertir($minutosTotales) {
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
    $query = "SELECT 
                categoria.Llave AS `Key`,
                categoria.Nombre AS Nombre,
                COUNT(asesoria.ID) AS Sesiones,
                COUNT(DISTINCT asesoria.Correo) AS Profesores,
                SUM(asesoria.Duracion) AS TotalHrsProf,
                SUM(DISTINCT asesoria.Duracion) AS TotalHrsTalent,
                AVG(DISTINCT asesoria.Duracion) AS DuracionMediaProf,
                AVG(asesoria.Duracion * (SELECT COUNT(id_Asesor) FROM asesoria_asesor WHERE asesoria_asesor.id_Asesoria = asesoria.ID)) AS DuracionMediaTalent
                FROM 
                asesoria
                INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
                INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
                INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
                INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
                WHERE 1=1";
    
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

    $query .= " GROUP BY categoria.ID";
    // Ejecutamos la consulta y mostramos los resultados
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<table class = 'table table-hover'>
                <thead class='table-head'>
                    <tr>
                        <th>Key</th>
                        <th>Nombre</th>
                        <th>Sesiones</th>
                        <th>Profesores</th>
                        <th>Total Horas Prof</th>
                        <th>Total Horas TALENT</th>
                        <th>Duración Media Prof</th>
                        <th>Duración Media TALENT</th>
                    </tr>
                </thead>
                <tbody class = 'table-group-divider'>";

        while ($row = $result->fetch_assoc()) {
            $totalHrsProf = (int) round($row["TotalHrsProf"]); // Convert float-string to int
            $totalHrsTalent = (int) round($row["TotalHrsTalent"]); // Convert float-string to int
            $duracionMediaProf = (int) round($row["DuracionMediaProf"]); // Convert float-string to int
            $duracionMediaTalent = (int) round($row["DuracionMediaTalent"]); // Convert float-string to int

            echo "<tr>";
            echo "<th class= border-bottom-0>" . $row["Key"] . "</th>";
            echo "<th class= border-bottom-0>" . $row["Nombre"] . "</th>";
            echo "<td>" . $row["Sesiones"] . "</td>";
            echo "<td>" . $row["Profesores"] . "</td>";
            echo "<td>" . convertir($totalHrsTalent) . "</td>";
            echo "<td>" . convertir($totalHrsProf) . "</td>";
            echo "<td>" . convertir($duracionMediaProf) . "</td>";
            echo "<td>" . convertir($duracionMediaTalent) . "</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";

    } else {
        echo "<tr><td colspan='6'>No se encontraron resultados</td></tr>";
    }

    $conn->close();
}
?>

<div id="categorias" class="mt-3 text-center"></div>