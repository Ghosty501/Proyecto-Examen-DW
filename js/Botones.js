let selectedFilters = {
    talent: [],
    sede: [],
    categoria: [],
    fechaInicio: '',
    fechaFin: ''
};

function selectOption(value, filterType, buttonId) {
    if (filterType === 'fechaInicio' || filterType === 'fechaFin') {
        // For date fields, store the value directly
        selectedFilters[filterType] = value;
        
    } else {
        if (!selectedFilters[filterType].includes(value)) {
            selectedFilters[filterType].push(value);
        }
        // Update the button text for dropdowns
        document.getElementById(buttonId).textContent = value;
    }

    // Display the selected filters in the 'filtros' div
    displaySelectedFilters();
}

function displaySelectedFilters() {
    let filterDisplay = document.querySelector('.filtros');
    filterDisplay.innerHTML = '';  // Clear previous filters

    // Handle date range
    let dateDisplay = "";
    if (selectedFilters.fechaInicio) {
        dateDisplay += `${selectedFilters.fechaInicio} :`;
    } else {
        dateDisplay += 'HASTA:';
    }
    
    if (selectedFilters.fechaFin) {
        dateDisplay += `${selectedFilters.fechaFin}`;
    } else {
        dateDisplay += '(ACTUAL)';
    }
    
    filterDisplay.innerHTML += `
        <div class="row">
            <div class="col-2">Fecha:</div>
            <div class="col-8">${dateDisplay}</div>
        </div>
    `;

    // Display the rest of the filters (talent, sede, categoria)
    for (let filter in selectedFilters) {
        if (selectedFilters[filter].length > 0) {
            if (Array.isArray(selectedFilters[filter])) {
                // Display all options from the same filter in one line
                let filterRow = `
                    <div class="row">
                        <div class="col-2">${filter.charAt(0).toUpperCase() + filter.slice(1)}:</div>
                        <div class="col-8">`;

                selectedFilters[filter].forEach((value) => {
                    filterRow += `
                        <span class="filter-item">
                            <button class="btn btn-secondary btn-sm" style="margin-left: 5px;" onclick="removeSpecificFilter('${filter}', '${value}')">${value} <i class="fa-solid fa-trash"></i></button>
                        </span>
                    `;
                });

                filterRow += `</div></div>`;
                filterDisplay.innerHTML += filterRow;
            }
        }
    }
}

function removeSpecificFilter(filterType, value) {
    // Remove a specific value from an array filter
    if (Array.isArray(selectedFilters[filterType])) {
        selectedFilters[filterType] = selectedFilters[filterType].filter(item => item !== value);
    }
    
    // Update the display
    displaySelectedFilters();
}


// Clear all filters
document.getElementById('limpiarBtn').addEventListener('click', function() {
    // Clear arrays
    selectedFilters = {
        talent: [],
        sede: [],
        categoria: [],
        fechaInicio: '',
        fechaFin: ''
    };

    // Reset dropdowns and clear displayed filters
    document.getElementById('dropdownTalentButton').textContent = 'Seleccione un miembro';
    document.getElementById('dropdownSedeButton').textContent = 'Todas las sedes';
    document.getElementById('dropdownCategoryButton').textContent = 'Seleccione una categoría';
    document.getElementById('fechaInicio').value = '';
    document.getElementById('fechaFin').value = '';
    displaySelectedFilters();
});

// Capture date changes
document.getElementById('fechaInicio').addEventListener('change', function() {
    selectOption(this.value, 'fechaInicio');
});
document.getElementById('fechaFin').addEventListener('change', function() {
    selectOption(this.value, 'fechaFin');
});

function buscar() {
    // Capture all selected filters
    let data = {
        categoria: selectedFilters.categoria,
        fechaInicio: selectedFilters.fechaInicio,
        fechaFin: selectedFilters.fechaFin,
        talent: selectedFilters.talent,
        sede: selectedFilters.sede
    };

    // Make the AJAX request to resumen.php for summary statistics
    $.ajax({
        url: 'components/resumen.php',
        method: 'POST',
        data: data,
        success: function(response) {
            // Replace the current summary with the new data
            document.querySelector('.cinta-resumen').innerHTML = response;
        },
        error: function(xhr, status, error) {
            console.error("Error fetching summary:", error);
            alert("An error occurred while fetching summary data. Please try again.");
        }
    });

    // Make the AJAX request to Resultados.php for detailed results
    $.ajax({
        url: 'Resultados.php',
        method: 'POST',
        data: data,
        success: function(response) {
            // Replace the current results with the new data
            document.querySelector('#resultado').innerHTML = response;
        },
        error: function(xhr, status, error) {
            console.error("Error fetching results:", error);
            alert("An error occurred while fetching results data. Please try again.");
        }
    });

    // Make the AJAX request to Categoria.php for category statistics
    $.ajax({
        url: 'Categoria.php',
        method: 'POST',
        data: data,
        success: function(response) {
            // Replace the current results with the new data
            document.querySelector('#categorias').innerHTML = response;
        },
        error: function(xhr, status, error) {
            console.error("Error fetching results:", error);
            alert("An error occurred while fetching results data. Please try again.");
        }
    });

    $.ajax({
        url: 'Asesores.php',
        method: 'POST',
        data: data,
        success: function(response) {
            // Replace the current results with the new data
            document.querySelector('#asesores').innerHTML = response;
        },
        error: function(xhr, status, error) {
            console.error("Error fetching results:", error);
            alert("An error occurred while fetching results data. Please try again.");
        }
    });
}


