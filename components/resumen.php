<?php
include 'sql.php';


$totalDuracion_ID = 0;
$totalDuracion_Correo = 0;
$totalDuracion_Asesor = 0;
$totalSesiones = 0;
$duracionMedia = 0;
$correosUnicos = [];
$idsUnicos = [];
$asesoresUnicos = [];


function convertirMinutosAFormatoHoras($minutosTotales) {

    $horas = floor($minutosTotales / 60); 
    $minutos = $minutosTotales % 60;      
    $minutos = sprintf('%02d', $minutos);
    return $horas . ':' . $minutos;
}


if (!empty($_POST)) {
   
    $categoria = $_POST['categoria'] ?? [];
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $talent = $_POST['talent'] ?? [];
    $sede = $_POST['sede'] ?? [];

    $query = "SELECT asesoria.ID, asesoria.Correo, asesoria.Fecha, asesoria.Duracion, 
                     categoria.Nombre AS Categoria, asesor.Nombre AS Asesor
              FROM asesoria
              INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
              INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
              INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
              INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
              WHERE 1=1"; 

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
            if (!in_array($row["ID"], $idsUnicos)) {
                $totalDuracion_ID += $row["Duracion"]; // Suma la duración solo la primera vez que se encuentre el ID
                $idsUnicos[] = $row["ID"]; // Guardamos el ID para evitar duplicados
                $totalSesiones++;
            } 


            if (!in_array($row["Correo"], $correosUnicos)) {
                $correosUnicos[] = $row["Correo"];
                $totalDuracion_Correo += $row["Duracion"];
            }
            
            $totalDuracion_Asesor += $row["Duracion"];
        }


        $duracionMedia = $totalSesiones > 0 ? $totalDuracion_ID / $totalSesiones : 0;
    }
}

$conn->close();
?>


<div class="cinta-resumen mt-3 text-center">
    <div class="row">
        <div class="col" id="sesiones"><?php echo $totalSesiones; ?></div>
        <div class="col" id="hrs-talent"><?php echo convertirMinutosAFormatoHoras(round($totalDuracion_Correo)); ?></div>
        <div class="col" id="duracion-media"><?php echo convertirMinutosAFormatoHoras(round($duracionMedia)); ?></div>
        <div class="col" id="hrs-profesor"><?php echo convertirMinutosAFormatoHoras(round($totalDuracion_Asesor)); ?></div>
        <div class="col" id="profesores"><?php echo count($correosUnicos); ?></div> <!-- Mostramos el número de correos únicos -->
    </div>
    <div class="row">
        <div class="col">Sesiones</div>
        <div class="col">Total Hrs. Profesor</div>
        <div class="col">Duración Media Sesión</div>
        <div class="col">Total Hrs. Talent</div>
        <div class="col">Profesores</div>
    </div>
</div>
