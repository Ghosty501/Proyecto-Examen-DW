    // Llamada AJAX para obtener los datos y actualizar los valores
function actualizarResumen() {
    $.ajax({
        url: 'components/resumen.php', // Reemplazar con la ruta correcta
        type: 'POST',
        data: {
            categoria: 'CategoriaSeleccionada', // Añade aquí los filtros necesarios
            fechaInicio: '2024-01-01', // Filtra según lo que tengas en tu aplicación
            fechaFin: '2024-12-31'
        },
        success: function(response) {
            // Parse the JSON response
            var data = JSON.parse(response);
            // Actualizar los valores en la cinta de resumen
            $('#sesiones').text(data.totalSesiones);
            $('#hrs-profesor').text(data.totalDuracion);
            $('#duracion-media').text(data.duracionPromedio.toFixed(2)); // Mostrar con 2 decimales
            $('#hrs-talent').text(data.totalHorasTalent || 0); // Si tienes este dato, de lo contrario, 0
            $('#profesores').text(data.totalProfesores || 0); // Si tienes este dato, de lo contrario, 0
            },
            error: function(xhr, status, error) {
                console.error("Error: " + error);
            }
        });
    }
    // Llamar a la función al cargar la págin
    $(document).ready(function() {
        actualizarResumen();
    });