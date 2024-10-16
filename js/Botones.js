function selectOption(value, dropdownId) {
    document.getElementById(dropdownId).textContent = value;
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