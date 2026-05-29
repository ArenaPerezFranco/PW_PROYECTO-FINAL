//Funcion para capturar datos de la tabla 
//y subirlos al formulario para editarlo
function editDestinatario(btn, idDestinatario){
    //buscaremos entre la fila tr ¿donde se hizo click?
    const fila = btn.closest('tr');

    //Aqui se optiene el texto de cada campo
    const nombre = fila.cells[1].innerText;
    const numero = fila.cells[2].innerText;
    const contacto = fila.cells[3].innerText;
    const calle = fila.cells[4].innerText;
    const colonia = fila.cells[5].innerText;
    const pais = fila.cells[6].innerText;

    //Se mandan valores obtenidos a los campos del formulario
    document.querySelector('input[name="nombre"]').value = nombre;
    document.querySelector('input[name="numero"]').value = numero;
    document.querySelector('input[name="contacto"]').value = contacto;
    document.querySelector('input[name="calle"]').value = calle;
    document.querySelector('input[name="colonia"]').value = colonia;

    //ELEMENTO SELECT EN PAIS:
    //Se busca la opción que coincida con el valor
    const selectPais = document.getElementById('pais');

    for (let i = 0; i < selectPais.options.length; i++) {
        if (selectPais.options[i].text === pais || selectPais.options[i].value === pais) {
            selectPais.selectedIndex = i;
            break;
        }
    }

    //Se guarda el ID para que PHP sepa que es una edición
    document.getElementById('id_destinatario_editar').value = idDestinatario;

    //CAMBIO DE ESTILO BOTON (estamos en modo edicion)
    const submitBtn = document.querySelector('.form-footer button');
    if (submitBtn) {
        submitBtn.textContent = 'ACTUALIZAR DESTINATARIO';
        submitBtn.style.backgroundColor = '#c12d2d';
        submitBtn.style.color = '#ffffff';
    }

    //SCROLL 
    window.scrollTo({
        top:0,
        behavior:'smooth'
    });
}

//---------ELIMINAR DESTINATARIO-----
function eliminarDestinatario(idDestinatario) {
    if (confirm(`¿Estás seguro de que deseas eliminar al destinatario con ID: ${idDestinatario}? Esta acción no se puede deshacer.`)) {
        // Creamos un formulario temporal para enviar el POST de manera segura a PHP
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = ''; // Se envía a la misma página

        const inputAccion = document.createElement('input');
        inputAccion.type = 'hidden';
        inputAccion.name = 'accion_eliminar';
        inputAccion.value = '1';

        const inputId = document.createElement('input');
        inputId.type = 'hidden';
        inputId.name = 'id_eliminar';
        inputId.value = idDestinatario;

        form.appendChild(inputAccion);
        form.appendChild(inputId);
        document.body.appendChild(form);
        form.submit();
    }
}

//FILTRO PARA BUSCAR EN LA TABLA
document.getElementById('searchDestinatario')?.addEventListener('input',function(){
    const term = this.value.toLowerCase().trim();
    const filas = document.querySelectorAll('#destinatariosTableBody tr');

    filas.forEach(fila =>{
        //Se ignoran las filas si no hay destinatarios registrados
        if (fila.querySelector('.no-data')) 
            return;
        //lo buscamos por medio de su ID
        const idDestinatario = fila.cells[0].innerText.toLowerCase();

        if (term === '' || idDestinatario.includes(term)) {
            fila.style.display = '';
        } else {
            fila.style.display = 'none';
        }

    });
});

//FUNCION PARA EXPORTAR TABLA EN FORMATO CSV
function exportDestinatariosCSV(){
    const filas = document.querySelectorAll('#destinatariosTableBody tr');
    if (filas.length === 0 || filas[0].querySelector('no-data')) {
        //S1: NO HAY DATOS EN LA TABLA/NO HAY DESTINATARIOS REGISTRADOS
        alert('No hay destinatarios para exportar');
        return;
    }
    const headers = ['ID', 'Nombre', 'Número', 'Contacto', 'Calle', 'Colonia', 'País'];
    const csvRows = [headers.join(',')];

    filas.forEach(fila =>{
        const rowData = [];
        //RECORRIDO:
        //Se recorre desde la primera columna hasta la 6 -> Queremos omitir la que es para editar
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
    a.download = 'destinatarios.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// BOtON FLOTANTE CSV
const exportBtn = document.createElement('button');
exportBtn.textContent = '📥 EXPORTAR DESTINATARIOS';
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
exportBtn.onclick = exportDestinatariosCSV;
document.body.appendChild(exportBtn);
