<?php
include 'components/sql.php';

// Check if there are POST filters; only then run the query
if (!empty($_POST)) {
    // Retrieve the filters from the POST request
    $categoria = $_POST['categoria'] ?? [];
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $talent = $_POST['talent'] ?? [];
    $sede = $_POST['sede'] ?? [];

    $totalDuracion = 0;
    $totalSesiones = 0;

    // Build the query based on filters
    $query = "SELECT asesoria.ID, asesoria.Correo, asesoria.Fecha, asesoria.Duracion, categoria.Nombre AS Categoria, asesor.Nombre AS Asesor
              FROM asesoria
              INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
              INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
              INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
              INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
              WHERE 1=1"; // Default condition to avoid syntax error

    // Apply filters if they exist
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

    // Execute the query and display the filtered results
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<table>
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
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["ID"] . "</td>";
            echo "<td>" . $row["Correo"] . "</td>";
            echo "<td>" . $row["Fecha"] . "</td>";
            echo "<td>" . $row["Duracion"] . "</td>";
            echo "<td>" . $row["Categoria"] . "</td>";
            echo "<td>" . $row["Asesor"] . "</td>";
            echo "</tr>";

            $totalDuracion += $row["Duracion"];
            $totalSesiones++;
        }

        echo "</tbody></table>";

        $duracionMedia = $totalSesiones > 0 ? $totalDuracion / $totalSesiones : 0;

        echo "<div class='cinta-resumen mt-3 text-center'>
                <div class='row'>
                    <div class='col' id='sesiones'>" . $totalSesiones . "</div>
                    <div class='col' id='hrs-profesor'>" . $totalDuracion . "</div>
                    <div class='col' id='duracion-media'>" . number_format($duracionMedia, 2) . "</div>
                    <div class='col' id='hrs-talent'>0</div> <!-- Assuming you have no data for this -->
                    <div class='col' id='profesores'>0</div> <!-- Assuming you have no data for this -->
                </div>
                <div class='row'>
                    <div class='col'>Sesiones</div>
                    <div class='col'>Total Hrs. Profesor</div>
                    <div class='col'>Duración Media Sesión</div>
                    <div class='col'>Total Hrs. Talent</div>
                    <div class='col'>Profesores</div>
                </div>
            </div>";

    } else {
        echo "<tr><td colspan='6'>No se encontraron resultados</td></tr>";
    }

    $conn->close();
}

?>

<div id="resultado" class="mt-3 text-center"></div>