// Funcion para capturar datos de la tabla y subirlos al formulario para editarlo
function editProveedor(btn, idProveedor){
    const fila = btn.closest('tr');

    const nombre = fila.cells[1].innerText;
    const numero = fila.cells[2].innerText;
    const contacto = fila.cells[3].innerText;
    const calle = fila.cells[4].innerText;
    const colonia = fila.cells[5].innerText;
    const pais = fila.cells[6].innerText;

    document.querySelector('input[name="nombre"]').value = nombre;
    document.querySelector('input[name="numero"]').value = numero;
    document.querySelector('input[name="contacto"]').value = contacto;
    document.querySelector('input[name="calle"]').value = calle;
    document.querySelector('input[name="colonia"]').value = colonia;

    const selectPais = document.getElementById('pais');
    for (let i = 0; i < selectPais.options.length; i++) {
        if (selectPais.options[i].text === pais || selectPais.options[i].value === pais) {
            selectPais.selectedIndex = i;
            break;
        }
    }

    // Vincula el ID oculto del formulario
    document.getElementById('id_proveedor_editar').value = idProveedor;

    const submitBtn = document.querySelector('.form-footer button');
    if (submitBtn) {
        submitBtn.textContent = 'ACTUALIZAR PROVEEDOR';
        submitBtn.style.backgroundColor = '#c12d2d';
        submitBtn.style.color = '#ffffff';
    }

    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}

//---------ELIMINAR PROVEEDOR-----
function eliminarProveedor(idProveedor) {
    if (confirm(`¿Estás seguro de que deseas eliminar al proveedor con ID: ${idProveedor}? Esta acción no se puede deshacer.`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = ''; 

        const inputAccion = document.createElement('input');
        inputAccion.type = 'hidden';
        inputAccion.name = 'accion_eliminar';
        inputAccion.value = '1';

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id_eliminar';
        inputId.value = idProveedor;

        form.appendChild(inputAccion);
        form.appendChild(inputId);
        document.body.appendChild(form);
        form.submit();
    }
}

// FILTRO PARA BUSCAR EN LA TABLA
document.getElementById('searchProveedor')?.addEventListener('input', function(){
    const term = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('#proveedoresTableBody tr');

    filas.forEach(fila => {
        if (fila.querySelector('.no-data')) 
            return;
        
        const idProveedor = fila.cells[0].innerText.toLowerCase();

        if (term === '' || idProveedor.includes(term)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }
    });
});

// FUNCION PARA EXPORTAR TABLA EN FORMATO CSV
function exportProveedoresCSV(){
    const filas = document.querySelectorAll('#proveedoresTableBody tr');
    if (filas.length === 0 || filas[0].querySelector('.no-data')) {
        alert('No hay proveedores para exportar');
        return;
    }
    const headers = ['ID', 'Nombre', 'Número', 'Contacto', 'Calle', 'Colonia', 'País'];
    const csvRows = [headers.join(',')];

    filas.forEach(fila => {
        const rowData = [];
        for (let index = 0; index <= 6; index++) {
            rowData.push(`"${fila.cells[index].innerText.trim()}"`);
        }
        csvRows.push(rowData.join(','));
    });

    const blob = new Blob([csvRows.join('\n')], { 
        type: 'text/csv;charset=utf-8;' 
    });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'proveedores.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// BOTON FLOTANTE CSV
const exportBtn = document.createElement('button');
exportBtn.textContent = '📥 EXPORTAR PROVEEDORES';
exportBtn.style.cssText = `
    position: fixed;
    bottom: 30px;
    right: 30px;
    padding: 12px 25px;
    background: #162844;
    color: white;
    border: none;
    border-radius: 25px;
    font-weight: bold;
    cursor: pointer;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
    z-index: 1000;
    transition: 0.2s;
`;
exportBtn.onmouseover = () => exportBtn.style.transform = 'translateY(-2px)';
exportBtn.onmouseout = () => exportBtn.style.transform = 'translateY(0)';
exportBtn.onclick = exportProveedoresCSV;
document.body.appendChild(exportBtn);
