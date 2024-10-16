<?php
include 'components/sql.php';
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sede = "SELECT id_Sede FROM Asesoria GROUP BY id_Sede";
$result_sede = $conn->query($sede);

$asesor = "SELECT Nombre FROM Asesor";
$result_asesor = $conn->query($asesor);

$categoria = "SELECT Nombre FROM Categoria";
$result_categoria = $conn->query($categoria);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Proyecto-Examen</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" 
    integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="css/Style.css">
</head>

<body>
    <div class="contenedor flex-column bg-black">
        <h1>Dashboard</h1>
        <h3>Filtros</h3>
        <hr>
        <div class="d-flex gap-3">
            <div class="input-group mb-3">
                <span class="input-group-text span-input">Inicio: </span>
                <input class="form-control my-input" type="date" id = "fechaInicio">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text span-input">Fin: </span>
                <input  class="form-control my-input" type="date" id = "fechaFin">
            </div>
            <div class="input-group mb-3">
                <span class="input-group-text span-input">Talent:</span>
                <button class="btn btn-secondary dropdown-toggle my-input" type="button" id="dropdownTalentButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Seleccione un miembro
                </button>
                <ul class="dropdown-menu">
                    <?php
                    if ($result_asesor->num_rows > 0) {
                        while ($row = $result_asesor->fetch_assoc()) {
                            echo "<li><button class='dropdown-item' type='button' onclick='selectOption(\"" . $row["Nombre"] . "\", \"dropdownTalentButton\")'>" . $row["Nombre"] . "</button></li>";
                        }
                    } else {
                        echo "<li><button class='dropdown-item' type='button'>No hay sedes disponibles</button></li>";
                    }
                    ?>
                </ul>
            </div>
            <button id="limpiarBtn" class="btn btn-grey mb-3">
                <!-- <input class="btn btn-grey" type="submit" value="Limpiar"> -->
                Limpiar 
                <i class="fa-solid fa-trash"></i>
            </button> 
        </div>

        <div class="d-flex gap-3">
            <div class="input-group mb-3">
                <span class="input-group-text span-input">Sede: </span>
                <button class="btn btn-secondary dropdown-toggle my-input" type="button" id="dropdownSedeButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Todas las sedes
                </button>
                <ul class="dropdown-menu">
                    <?php
                    if ($result_sede->num_rows > 0) {
                        while ($row = $result_sede->fetch_assoc()) {
                            switch ($row["id_Sede"]) {
                                case 1:
                                    $row["id_Sede"] = "México";
                                    break;
                                case 4:
                                    $row["id_Sede"] = "Aguascalientes";
                                    break;
                                case 5:
                                    $row["id_Sede"] = "Guadalajara";
                                    break;
                                case 6:
                                    $row["id_Sede"] = "Ciudad UP";
                                    break;
                                case 1007:
                                    $row["id_Sede"] = "Sin Sede";
                                    break;
                            }
                            echo "<li><button class='dropdown-item' type='button' onclick='selectOption(\"" . $row["id_Sede"] . "\", \"dropdownSedeButton\")'>" . $row["id_Sede"] . "</button></li>";
                        }
                    } else {
                        echo "<li><button class='dropdown-item' type='button'>No hay sedes disponibles</button></li>";
                    }
                    ?>
                </ul>
            </div>
            
            <div class="input-group mb-3">
                <span class="input-group-text span-input">Categoría:</span>
                <button class="btn btn-secondary dropdown-toggle my-input" type="button" id="dropdownCategoryButton" data-bs-toggle="dropdown" aria-expanded="false">
                    Seleccione una categoría
                </button>
                <ul class="dropdown-menu">
                    <?php
                    if ($result_categoria->num_rows > 0) {
                        while ($row = $result_categoria->fetch_assoc()) {
                            echo "<li><button class='dropdown-item' type='button' onclick='selectOption(\"" . $row["Nombre"] . "\", \"dropdownCategoryButton\")'>" . $row["Nombre"] . "</button></li>";
                        }
                    } else {
                        echo "<li><button class='dropdown-item' type='button'>No hay categorías disponibles</button></li>";
                    }
                    ?>
                </ul>
            </div>
            <button class="btn btn-gold mb-3">
                <!-- <input class="btn btn-gold" type="submit" value="Buscar"> -->
                <i class="fa-solid fa-magnifying-glass"></i>
                Buscar
            </button> 
        </div>
        <div class="filtros">
            <div class="row">
                <div class="col-2">
                    Intervalo de Fechas:
                </div>
                <div class="col-10">
                    HASTA: (ACTUAL)
                </div>
            </div>
            <div class="row">
                <div class="col-2">
                    Miembro Talent:
                </div>
                <div class="col-10">
                    Espacio vacío
                </div>
            </div>
        </div>

        <div>
            <?php include 'components/resumen.php'; ?>
        </div>

        <div>
            <?php include 'components/nav-tabs.php'; ?>
        </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="js/Botones.js"></script>
</body>
</html>
