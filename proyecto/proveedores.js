let proveedores = [];

function loadProveedoresFromStorage() {
    const stored = localStorage.getItem('proveedores');
    if (stored) {
        proveedores = JSON.parse(stored);
        renderProveedoresTable();
        updateProveedoresSummary();
    }
}

function saveProveedoresToStorage() {
    localStorage.setItem('proveedores', JSON.stringify(proveedores));
}

function renderProveedoresTable() {
    const tbody = document.getElementById('proveedoresTableBody');
    const searchTerm = document.getElementById('searchProveedor')?.value.toLowerCase() || '';
    const filterStatus = document.getElementById('filterStatus')?.value || '';

    let filteredProveedores = proveedores;

    if (searchTerm) {
        filteredProveedores = filteredProveedores.filter(proveedor =>
            proveedor.nombre.toLowerCase().includes(searchTerm) ||
            proveedor.contacto.toLowerCase().includes(searchTerm)
        );
    }

    if (filterStatus) {
        filteredProveedores = filteredProveedores.filter(proveedor =>
            proveedor.estado === filterStatus
        );
    }

    if (filteredProveedores.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" class="empty-message">No hay proveedores registrados</td></tr>';
        return;
    }

    tbody.innerHTML = filteredProveedores.map((proveedor, index) => `
        <tr>
            <td>${escapeHtml(proveedor.nombre)}</td>
            <td>${escapeHtml(proveedor.numero)}</td>
            <td>${escapeHtml(proveedor.entidad_fiscal)}</td>
            <td>${escapeHtml(proveedor.calle)}</td>
            <td>${escapeHtml(proveedor.contacto)}</td>
            <td>${escapeHtml(proveedor.colonia)}</td>
            <td>${escapeHtml(proveedor.pais)}</td>
            <td>
                <button class="btn-edit" onclick="editProveedor(${index})">✏️ Editar</button>
                <button class="btn-delete" onclick="deleteProveedor(${index})">🗑️ Eliminar</button>
            </td>
        </tr>
    `).join('');

    updateProveedoresSummary();
}

function updateProveedoresSummary() {
    const total = proveedores.length;
    const activos = proveedores.filter(p => p.estado === 'Activo').length;
    const inactivos = proveedores.filter(p => p.estado === 'Inactivo').length;

    document.getElementById('totalProveedores').textContent = total;
    document.getElementById('proveedoresActivos').textContent = activos;
    document.getElementById('proveedoresInactivos').textContent = inactivos;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function addProveedor(data) {
    const nuevo = {
        nombre: data.nombre,
        numero: data.numero,
        entidad_fiscal: data.entidad_fiscal,
        calle: data.calle,
        contacto: data.contacto,
        colonia: data.colonia,
        pais: data.pais,
        estado: data.estado || 'Activo'
    };

    proveedores.push(nuevo);
    saveProveedoresToStorage();
    renderProveedoresTable();
    return true;
}

function editProveedor(index) {
    const proveedor = proveedores[index];

    document.querySelector('input[name="nombre"]').value = proveedor.nombre;
    document.querySelector('input[name="numero"]').value = proveedor.numero;
    document.querySelector('input[name="entidad_fiscal"]').value = proveedor.entidad_fiscal;
    document.querySelector('input[name="calle"]').value = proveedor.calle;
    document.querySelector('input[name="contacto"]').value = proveedor.contacto;
    document.querySelector('input[name="colonia"]').value = proveedor.colonia;
    document.querySelector('input[name="pais"]').value = proveedor.pais;

    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'ACTUALIZAR PROVEEDOR';

    proveedores.splice(index, 1);

    const handleSubmit = () => {
        submitBtn.textContent = originalText;
        submitBtn.removeEventListener('click', handleSubmit);
        saveProveedoresToStorage();
        renderProveedoresTable();
    };

    submitBtn.addEventListener('click', handleSubmit, { once: true });
}

function deleteProveedor(index) {
    if (confirm(`¿Eliminar proveedor "${proveedores[index].nombre}"?`)) {
        proveedores.splice(index, 1);
        saveProveedoresToStorage();
        renderProveedoresTable();
        alert('Proveedor eliminado correctamente');
    }
}

document.getElementById("proveedoresForm")?.addEventListener("submit", function(e) {
    e.preventDefault();

    if (!this.nombre.value || !this.numero.value || !this.entidad_fiscal.value || !this.calle.value) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }

    const data = {
        nombre: this.nombre.value,
        numero: this.numero.value,
        entidad_fiscal: this.entidad_fiscal.value,
        calle: this.calle.value,
        contacto: this.contacto.value,
        colonia: this.colonia.value,
        pais: this.pais.value,
        estado: this.estado?.value || 'Activo'
    };

    addProveedor(data);
    this.reset();
    alert('Proveedor agregado exitosamente');
});

function exportProveedoresToCSV() {
    if (proveedores.length === 0) {
        alert('No hay proveedores para exportar');
        return;
    }

    const headers = ['Nombre', 'Número', 'Entidad Fiscal', 'Calle', 'Contacto', 'Colonia', 'País', 'Estado'];
    const csvRows = [];
    csvRows.push(headers.join(','));

    for (const p of proveedores) {
        const row = [
            `"${p.nombre}"`,
            `"${p.numero}"`,
            `"${p.entidad_fiscal}"`,
            `"${p.calle}"`,
            `"${p.contacto}"`,
            `"${p.colonia}"`,
            `"${p.pais}"`,
            `"${p.estado}"`
        ];
        csvRows.push(row.join(','));
    }

    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'proveedores.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

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
exportBtn.onclick = exportProveedoresToCSV;
document.body.appendChild(exportBtn);

loadProveedoresFromStorage();
