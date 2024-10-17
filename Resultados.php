<?php
include 'components/sql.php';

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Recupera los filtros
$categoria = $_POST['categoria'] ?? '';
$fechaInicio = $_POST['fechaInicio'] ?? '';
$fechaFin = $_POST['fechaFin'] ?? '';
?>

<div>
    <table>
        <thead class="table-head">
            <tr>
                <th>ID</th>
                <th>Correo</th>
                <th>Fecha</th>
                <th>Duración</th>
                <th>Categoría</th>
                <th>Asesor</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Asegúrate de que los filtros estén completos antes de realizar la consulta
            if (!empty($categoria) && !empty($fechaInicio) && !empty($fechaFin)) {
                $sql = "SELECT asesoria.ID, asesoria.Correo, asesoria.Fecha, asesoria.Duracion, categoria.Nombre AS Categoria, asesor.Nombre AS Asesor
                        FROM asesoria
                        INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
                        INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
                        INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
                        WHERE 1=1";

                // Escapa los valores antes de agregar a la consulta
                $sql .= " AND categoria.Nombre = '" . $conn->real_escape_string($categoria) . "'";
                $sql .= " AND DATE(asesoria.Fecha) BETWEEN '" . $conn->real_escape_string($fechaInicio) . "' AND '" . $conn->real_escape_string($fechaFin) . "'";

                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
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
                } else {
                    echo "<tr><td colspan='6'>No se encontraron resultados</td></tr>";
                }
            } else {
                // Si los filtros no están completos, no se genera nada
                echo "<tr><td colspan='6'>Por favor, complete todos los filtros y haga clic en Buscar.</td></tr>";
            }

            ?>
        </tbody>
    </table>
</div>




