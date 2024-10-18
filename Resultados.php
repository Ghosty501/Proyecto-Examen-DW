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
        }

        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='6'>No se encontraron resultados</td></tr>";
    }

    $conn->close();
}

?>

<div id="resultado" class="mt-3 text-center"></div>