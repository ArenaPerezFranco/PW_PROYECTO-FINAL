let destinatarios = [];

function loadDestinatariosFromStorage() {
    const stored = localStorage.getItem('destinatarios');
    if (stored) {
        destinatarios = JSON.parse(stored);
        renderDestinatariosTable();
        updateDestinatariosSummary();
    }
}

function saveDestinatariosToStorage() {
    localStorage.setItem('destinatarios', JSON.stringify(destinatarios));
}

function renderDestinatariosTable() {
    const tbody = document.getElementById('destinatariosTableBody');
    const searchTerm = document.getElementById('searchDestinatario')?.value.toLowerCase() || '';
    const filterStatus = document.getElementById('filterStatus')?.value || '';

    let filtered = destinatarios;

    if (searchTerm) {
        filtered = filtered.filter(d =>
            d.nombre.toLowerCase().includes(searchTerm) ||
            d.contacto.toLowerCase().includes(searchTerm)
        );
    }

    if (filterStatus) {
        filtered = filtered.filter(d => d.estado === filterStatus);
    }

    if (filtered.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-message">No hay destinatarios registrados</td></tr>';
        return;
    }

    tbody.innerHTML = filtered.map((d, index) => `
        <tr>
            <td>${escapeHtml(d.nombre)}</td>
            <td>${escapeHtml(d.numero)}</td>
            <td>${escapeHtml(d.contacto)}</td>
            <td>${escapeHtml(d.calle)}</td>
            <td>${escapeHtml(d.colonia)}</td>
            <td>${escapeHtml(d.pais)}</td>
            <td>
                <button class="btn-edit" onclick="editDestinatario(${index})">✏️ Editar</button>
                <button class="btn-delete" onclick="deleteDestinatario(${index})">🗑️ Eliminar</button>
            </td>
        </tr>
    `).join('');

    updateDestinatariosSummary();
}

function updateDestinatariosSummary() {
    const total = destinatarios.length;
    const activos = destinatarios.filter(d => d.estado === 'Activo').length;
    const inactivos = destinatarios.filter(d => d.estado === 'Inactivo').length;

    document.getElementById('totalDestinatarios').textContent = total;
    document.getElementById('destinatariosActivos').textContent = activos;
    document.getElementById('destinatariosInactivos').textContent = inactivos;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function addDestinatario(data) {
    const nuevo = {
        nombre: data.nombre,
        numero: data.numero,
        contacto: data.contacto,
        calle: data.calle,
        colonia: data.colonia,
        pais: data.pais,
        estado: data.estado || 'Activo'
    };

    destinatarios.push(nuevo);
    saveDestinatariosToStorage();
    renderDestinatariosTable();
    return true;
}

function editDestinatario(index) {
    const d = destinatarios[index];

    document.querySelector('input[name="nombre"]').value = d.nombre;
    document.querySelector('input[name="numero"]').value = d.numero;
    document.querySelector('input[name="contacto"]').value = d.contacto;
    document.querySelector('input[name="calle"]').value = d.calle;
    document.querySelector('input[name="colonia"]').value = d.colonia;
    document.querySelector('input[name="pais"]').value = d.pais;

    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'ACTUALIZAR DESTINATARIO';

    destinatarios.splice(index, 1);

    const handleSubmit = () => {
        submitBtn.textContent = originalText;
        submitBtn.removeEventListener('click', handleSubmit);
        saveDestinatariosToStorage();
        renderDestinatariosTable();
    };

    submitBtn.addEventListener('click', handleSubmit, { once: true });
}

function deleteDestinatario(index) {
    if (confirm(`¿Eliminar destinatario "${destinatarios[index].nombre}"?`)) {
        destinatarios.splice(index, 1);
        saveDestinatariosToStorage();
        renderDestinatariosTable();
        alert('Destinatario eliminado correctamente');
    }
}

document.getElementById("destinatariosForm")?.addEventListener("submit", function(e) {
    e.preventDefault();

    if (!this.nombre.value || !this.numero.value || !this.contacto.value || !this.calle.value) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }

    const data = {
        nombre: this.nombre.value,
        numero: this.numero.value,
        contacto: this.contacto.value,
        calle: this.calle.value,
        colonia: this.colonia.value,
        pais: this.pais.value,
        estado: this.estado?.value || 'Activo'
    };

    addDestinatario(data);
    this.reset();
    alert('Destinatario agregado exitosamente');
});

function exportDestinatariosToCSV() {
    if (destinatarios.length === 0) {
        alert('No hay destinatarios para exportar');
        return;
    }

    const headers = ['Nombre', 'Número', 'Contacto', 'Calle', 'Colonia', 'País', 'Estado'];
    const csvRows = [];
    csvRows.push(headers.join(','));

    for (const d of destinatarios) {
        const row = [
            `"${d.nombre}"`,
            `"${d.numero}"`,
            `"${d.contacto}"`,
            `"${d.calle}"`,
            `"${d.colonia}"`,
            `"${d.pais}"`,
            `"${d.estado}"`
        ];
        csvRows.push(row.join(','));
    }

    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'destinatarios.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

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
exportBtn.onclick = exportDestinatariosToCSV;
document.body.appendChild(exportBtn);

loadDestinatariosFromStorage();