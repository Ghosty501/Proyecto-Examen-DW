<?php
include 'components/sql.php';

// Función para convertir minutos a formato de horas:minutos
function convertirahoras($minutosTotales) {
    $horas = floor($minutosTotales / 60);
    $minutos = $minutosTotales % 60;
    $minutos = sprintf('%02d', $minutos);
    return $horas . ':' . $minutos;
}

function separarNombre($nombreCompleto) {
    $partes = explode(' ', $nombreCompleto);
    $numPartes = count($partes);

    // Si tiene más de dos palabras, asumimos que las últimas dos son los apellidos
    if ($numPartes > 2) {
        $apellidos = $partes[$numPartes - 2] . ' ' . $partes[$numPartes - 1];
        $nombres = implode(' ', array_slice($partes, 0, $numPartes - 2));
    } else {
        // Si solo tiene dos palabras, una para nombre y otra para apellido
        $nombres = $partes[0];
        $apellidos = $partes[1] ?? '';
    }

    return ['nombres' => $nombres, 'apellidos' => $apellidos];
}

// Verificamos si se han enviado filtros a través del formulario
if (!empty($_POST)) {
    $categoria = $_POST['categoria'] ?? [];
    $fechaInicio = $_POST['fechaInicio'] ?? '';
    $fechaFin = $_POST['fechaFin'] ?? '';
    $talent = $_POST['talent'] ?? [];
    $sede = $_POST['sede'] ?? [];


    // Calculamos el total global de horas talent con los filtros
    $totalHrsTalentGlobalQuery = "SELECT SUM(asesoria.Duracion) AS TotalHrsTalentGlobal
                                  FROM asesoria
                                  INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
                    INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
                    INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
                    INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
                                  WHERE 1=1";
    if (!empty($fechaInicio)) {
        $totalHrsTalentGlobalQuery .= " AND asesoria.Fecha >= STR_TO_DATE('" . $conn->real_escape_string($fechaInicio) . "', '%Y-%m-%d')";
    }
    if (!empty($fechaFin)) {
        $totalHrsTalentGlobalQuery .= " AND asesoria.Fecha <= STR_TO_DATE('" . $conn->real_escape_string($fechaFin) . "', '%Y-%m-%d')";
    }
    if (!empty($categoria) && is_array($categoria)) {
        $totalHrsTalentGlobalQuery .= " AND categoria.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $categoria)) . "')";
    }
    if (!empty($talent) && is_array($talent)) {
        $totalHrsTalentGlobalQuery .= " AND asesor.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $talent)) . "')";
    }
    if (!empty($sede) && is_array($sede)) {
        $totalHrsTalentGlobalQuery .= " AND sede.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $sede)) . "')";
    }

    $resultGlobal = $conn->query($totalHrsTalentGlobalQuery);
    $totalHrsTalentGlobal = ($resultGlobal && $resultGlobal->num_rows > 0) ? $resultGlobal->fetch_assoc()["TotalHrsTalentGlobal"] : 1;

    // Calculamos el total de horas de profesores con los filtros
    $totalHrsProfQuery = "SELECT SUM(asesoria.Duracion) AS TotalHrsProf
                          FROM asesoria
                          INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
                    INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
                    INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
                    INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
                          WHERE 1=1";

    if (!empty($fechaInicio)) {
        $totalHrsProfQuery .= " AND asesoria.Fecha >= STR_TO_DATE('" . $conn->real_escape_string($fechaInicio) . "', '%Y-%m-%d')";
    }
    if (!empty($fechaFin)) {
        $totalHrsProfQuery .= " AND asesoria.Fecha <= STR_TO_DATE('" . $conn->real_escape_string($fechaFin) . "', '%Y-%m-%d')";
    }
    if (!empty($categoria) && is_array($categoria)) {
        $totalHrsProfQuery .= " AND categoria.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $categoria)) . "')";
    }
    if (!empty($talent) && is_array($talent)) {
        $totalHrsProfQuery .= " AND asesor.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $talent)) . "')";
    }
    if (!empty($sede) && is_array($sede)) {
        $totalHrsProfQuery .= " AND sede.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $sede)) . "')";
    }

    $resultProf = $conn->query($totalHrsProfQuery);
    $totalHrsProf = $resultProf->fetch_assoc()["TotalHrsProf"];

    $sesionesUnicas = [];
    $totalDuracion_Correo = 0;

        $query_duracion_profes = "SELECT asesoria.ID, asesoria.Duracion
        FROM asesoria
        INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
        INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
        INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
        INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
        WHERE 1=1"; 

        if (!empty($fechaInicio)) {
        $query_duracion_profes .= " AND asesoria.Fecha >= STR_TO_DATE('" . $conn->real_escape_string($fechaInicio) . "', '%Y-%m-%d')";
        }
        if (!empty($fechaFin)) {
        $query_duracion_profes .= " AND asesoria.Fecha <= STR_TO_DATE('" . $conn->real_escape_string($fechaFin) . "', '%Y-%m-%d')";
        }
        if (!empty($categoria) && is_array($categoria)) {
        $query_duracion_profes .= " AND categoria.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $categoria)) . "')";
        }
        if (!empty($talent) && is_array($talent)) {
        $query_duracion_profes .= " AND asesor.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $talent)) . "')";
        }
        if (!empty($sede) && is_array($sede)) {
        $query_duracion_profes .= " AND sede.Nombre IN ('" . implode("','", array_map([$conn, 'real_escape_string'], $sede)) . "')";
        }

        // Ejecutamos la consulta con los filtros
        $result_prof_duracion = $conn->query($query_duracion_profes);

        // Calculamos los totales
        if ($result_prof_duracion && $result_prof_duracion->num_rows > 0) {
            while ($row_calc = $result_prof_duracion->fetch_assoc()) {

                if (!in_array($row_calc["ID"], $sesionesUnicas)) {
                $sesionesUnicas[] = $row_calc["ID"];
                $totalDuracion_Correo += $row_calc["Duracion"];
                }
            }
        }




    // Construimos la consulta principal para asesores
    $query = "SELECT 
                asesor.Correo,
                asesor.Nombre AS Nombres,
                asesor.Nombre AS Apellidos,
                COUNT(DISTINCT asesoria.ID) AS Sesiones,
                SUM(asesoria.Duracion) AS TotalHrsTalent,
                AVG(asesoria.Duracion) AS DuracionMediaSesion,
                (SUM(asesoria.Duracion) / $totalDuracion_Correo) * 100 AS PorcentajeHorasProf,
                (SUM(asesoria.Duracion) / $totalHrsTalentGlobal) * 100 AS PorcentajeHorasTalent
              FROM asesoria
              INNER JOIN asesoria_asesor ON asesoria.ID = asesoria_asesor.id_Asesoria
              INNER JOIN asesor ON asesoria_asesor.id_Asesor = asesor.ID
              INNER JOIN categoria ON asesoria.id_Categoria = categoria.ID
              INNER JOIN sede ON asesoria.id_Sede = sede.id_Sede
              WHERE 1=1";

    // Apply filters to the main query
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

    $query .= " GROUP BY asesor.Correo";
//echo $query; // Para la consulta principal
    // Ejecutamos la consulta y mostramos los resultados
    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        echo "<table class='table table-hover'>
                <thead class='table-head'>
                    <tr>
                        <th>Correo</th>
                        <th>Nombres</th>
                        <th>Apellidos</th>
                        <th>Sesiones</th>
                        <th>Total Horas TALENT</th>
                        <th>Duración Media Sesión</th>
                        <th>% Horas Prof</th>
                        <th>% Horas TALENT</th>
                    </tr>
                </thead>
                <tbody class='table-group-divider'>";

        while ($row = $result->fetch_assoc()) {
            if($row["Nombres"] == "Monserrat Villacampa Espinosa de los Monteros"){
                $nombreSeparado['nombres'] = "Montserrat";
                $nombreSeparado['apellidos'] = "Villacampa Espinosa de los Monteros";
            } elseif ($row["Nombres"] == "Mariana De la Mora Figueroa"){
                $nombreSeparado['nombres'] = "Mariana";
                $nombreSeparado['apellidos'] = "De la Mora Figueroa";
            } else {$nombreSeparado = separarNombre($row["Nombres"]);}
            $totalHrsTalent = (int) round($row["TotalHrsTalent"]);
            $duracionMediaSesion = (int) round($row["DuracionMediaSesion"]);
            $porcentajeHorasProf = round($row["PorcentajeHorasProf"], 2);
            $porcentajeHorasTalent = round($row["PorcentajeHorasTalent"], 2);

            echo "<tr>";
            echo "<td>" . $row["Correo"] . "</td>";
            echo "<td>" . $nombreSeparado['nombres'] . "</td>";
            echo "<td>" . $nombreSeparado['apellidos'] . "</td>";
            echo "<td>" . $row["Sesiones"] . "</td>";
            echo "<td>" . convertirahoras($totalHrsTalent) . "</td>";
            echo "<td>" . convertirahoras($duracionMediaSesion) . "</td>";
            echo "<td>" . $porcentajeHorasProf . "%</td>";
            echo "<td>" . $porcentajeHorasTalent . "%</td>";
            echo "</tr>";
        }

        echo "</tbody></table>";
    } else {
        echo "<tr><td colspan='8'>No se encontraron resultados</td></tr>";
    }

    $conn->close();
}
?>

<div id="asesores" class="mt-3 text-center"></div>