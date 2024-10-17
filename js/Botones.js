function selectOption(value, inputId, buttonId) {
    // Almacena el valor en el campo oculto
    $('#' + inputId).val(value);
    // Cambia el texto del botón al valor seleccionado
    $('#' + buttonId).text(value);
}




// Función para limpiar los filtros
document.getElementById('limpiarBtn').addEventListener('click', function() {
    // Restablecer los dropdowns
    document.getElementById('dropdownTalentButton').textContent = 'Seleccione un miembro';
    document.getElementById('dropdownSedeButton').textContent = 'Todas las sedes';
    document.getElementById('dropdownCategoryButton').textContent = 'Seleccione una categoría';

    // Limpiar los valores de los campos de fecha
    document.getElementById('fechaInicio').value = '';
    document.getElementById('fechaFin').value = '';
});


function buscar() {
    // Captura los valores de los filtros
    const categoria = $('#categoryInput').val(); // Asegúrate de que el ID sea correcto
    const fechaInicio = $('input[name="fechaInicio"]').val(); // Asegúrate de que el name sea correcto
    const fechaFin = $('input[name="fechaFin"]').val(); // Asegúrate de que el name sea correcto

    // Verifica que los filtros estén llenos
    if (!categoria || !fechaInicio || !fechaFin) {
        alert("Por favor, complete todos los filtros antes de buscar.");
        return; // Detiene la ejecución si los filtros no están completos
    }

    $.ajax({
        url: 'Resultados.php',  // Cambia a la ruta del archivo PHP que procesará la solicitud
        method: 'POST',
        data: {
            categoria: categoria,
            fechaInicio: fechaInicio,
            fechaFin: fechaFin
        },
        success: function(response) {
            $('#resultado').html(response); // Muestra la respuesta en el <tbody> con id "resultado"
        },
        error: function(xhr, status, error) {
            console.error("Error: " + error);
        }
    });
}



