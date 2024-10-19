<?php
include 'components/sql.php';

// Definimos variables para los cálculos
$totalDuracion = 0;
$totalSesiones = 0;
$duracionMedia = 0;

// Verificamos si se han enviado filtros a través del formulario
if (!empty($_POST)) {
    // Recuperamos los filtros del formulario (enviados por POST)
    $categoria = $_POST['categoria'] ?? [];
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $talent = $_POST['talent'] ?? [];
    $sede = $_POST['sede'] ?? [];

    // Construimos la consulta SQL base
    $query = "SELECT asesoria.ID, asesoria.Correo, asesoria.Fecha, asesoria.Duracion, 
                     categoria.Nombre AS Categoria, asesor.Nombre AS Asesor
              FROM asesoria
              INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
              INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
              INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
              INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
              WHERE 1=1"; // Añadimos WHERE 1=1 para facilitar la concatenación de filtros

    // Añadimos filtros dinámicos según los valores enviados
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

    // Ejecutamos la consulta con los filtros
    $result = $conn->query($query);

    // Calculamos los totales
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $totalDuracion += $row["Duracion"];
            $totalSesiones++;
        }

        // Calculamos la duración media
        $duracionMedia = $totalSesiones > 0 ? $totalDuracion / $totalSesiones : 0;
    }
}

$conn->close();
?>

<!-- Generamos la barra resumen con los resultados filtrados -->
<div class="cinta-resumen mt-3 text-center">
    <div class="row">
        <div class="col" id="sesiones"><?php echo $totalSesiones; ?></div>
        <div class="col" id="hrs-profesor"><?php echo $totalDuracion; ?></div>
        <div class="col" id="duracion-media"><?php echo number_format($duracionMedia, 2); ?></div>
        <div class="col" id="hrs-talent">0</div> <!-- Puedes agregar más lógica si es necesario -->
        <div class="col" id="profesores">0</div> <!-- Puedes agregar más lógica si es necesario -->
    </div>
    <div class="row">
        <div class="col">Sesiones</div>
        <div class="col">Total Hrs. Profesor</div>
        <div class="col">Duración Media Sesión</div>
        <div class="col">Total Hrs. Talent</div>
        <div class="col">Profesores</div>
    </div>
</div>
