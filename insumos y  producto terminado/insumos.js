// Array para almacenar insumos
let insumos = [];

// Cargar insumos del localStorage al iniciar
function loadInsumosFromStorage() {
    const stored = localStorage.getItem('insumos');
    if (stored) {
        insumos = JSON.parse(stored);
        renderTable();
        updateSummary();
    }
}

// Guardar insumos en localStorage
function saveInsumosToStorage() {
    localStorage.setItem('insumos', JSON.stringify(insumos));
}

// Determinar estado del stock
function getStockStatus(cantidad, stockMinimo) {
    if (cantidad <= 0) return { text: 'SIN STOCK', class: 'status-critico' };
    if (cantidad <= stockMinimo) return { text: 'STOCK BAJO', class: 'status-bajo' };
    return { text: 'NORMAL', class: 'status-normal' };
}

// Renderizar la tabla
function renderTable() {
    const tbody = document.getElementById('tableBody');
    const searchTerm = document.getElementById('searchInput')?.value.toLowerCase() || '';
    const filterUnidad = document.getElementById('filterUnidad')?.value || '';
    
    let filteredInsumos = insumos;
    
    // Aplicar filtro de búsqueda
    if (searchTerm) {
        filteredInsumos = filteredInsumos.filter(insumo => 
            insumo.nombre_insumo.toLowerCase().includes(searchTerm) ||
            insumo.proveedor.toLowerCase().includes(searchTerm)
        );
    }
    
    // Aplicar filtro de unidad
    if (filterUnidad) {
        filteredInsumos = filteredInsumos.filter(insumo => 
            insumo.unidad === filterUnidad
        );
    }
    
    if (filteredInsumos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="10" class="empty-message">No hay insumos registrados</td></tr>';
        return;
    }
    
    tbody.innerHTML = filteredInsumos.map((insumo, originalIndex) => {
        // Encontrar el índice original para editar/eliminar
        const index = insumos.findIndex(i => i === insumo);
        const status = getStockStatus(insumo.cantidad, insumo.stock_minimo || 0);
        const valorTotal = insumo.cantidad * insumo.costo;
        
        return `
            <tr>
                <td>${escapeHtml(insumo.nombre_insumo)}</td>
                <td>${escapeHtml(insumo.unidad)}</td>
                <td>${parseFloat(insumo.cantidad).toFixed(2)}</td>
                <td>$${parseFloat(insumo.costo).toFixed(2)}</td>
                <td>$${valorTotal.toFixed(2)}</td>
                <td>${escapeHtml(insumo.proveedor || '-')}</td>
                <td>${insumo.stock_minimo ? parseFloat(insumo.stock_minimo).toFixed(2) : '-'}</td>
                <td><span class="status-badge ${status.class}">${status.text}</span></td>
                <td>${escapeHtml(insumo.descripcion || '-')}</td>
                <td>
                    <button class="btn-edit" onclick="editInsumo(${index})">✏️ Editar</button>
                    <button class="btn-delete" onclick="deleteInsumo(${index})">🗑️ Eliminar</button>
                </td>
            </tr>
        `;
    }).join('');
    
    updateSummary();
}

// Actualizar resumen
function updateSummary() {
    const totalInsumos = insumos.length;
    const valorTotalInventario = insumos.reduce((total, insumo) => {
        return total + (insumo.cantidad * insumo.costo);
    }, 0);
    
    const stockBajo = insumos.filter(insumo => {
        const stockMinimo = insumo.stock_minimo || 0;
        return insumo.cantidad <= stockMinimo && insumo.cantidad > 0;
    }).length;
    
    document.getElementById('totalInsumos').textContent = totalInsumos;
    document.getElementById('valorTotalInventario').textContent = `$${valorTotalInventario.toFixed(2)}`;
    document.getElementById('stockBajo').textContent = stockBajo;
}

// Función para escapar HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Agregar nuevo insumo
function addInsumo(insumoData) {
    const newInsumo = {
        nombre_insumo: insumoData.nombre_insumo,
        unidad: insumoData.unidad,
        cantidad: parseFloat(insumoData.cantidad),
        costo: parseFloat(insumoData.costo),
        proveedor: insumoData.proveedor || '',
        stock_minimo: insumoData.stock_minimo ? parseFloat(insumoData.stock_minimo) : 0,
        descripcion: insumoData.descripcion || ''
    };
    
    insumos.push(newInsumo);
    saveInsumosToStorage();
    renderTable();
    return true;
}

// Editar insumo
function editInsumo(index) {
    const insumo = insumos[index];
    
    // Llenar el formulario con los datos del insumo
    document.querySelector('input[name="nombre_insumo"]').value = insumo.nombre_insumo;
    document.querySelector('input[name="unidad"]').value = insumo.unidad;
    document.querySelector('input[name="cantidad"]').value = insumo.cantidad;
    document.querySelector('input[name="costo"]').value = insumo.costo;
    document.querySelector('input[name="proveedor"]').value = insumo.proveedor || '';
    document.querySelector('input[name="stock_minimo"]').value = insumo.stock_minimo || '';
    document.querySelector('textarea[name="descripcion"]').value = insumo.descripcion || '';
    
    // Cambiar el texto del botón temporalmente
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'ACTUALIZAR INSUMO';
    
    // Eliminar el insumo actual y preparar para actualización
    insumos.splice(index, 1);
    
    // Scroll suave al formulario
    document.querySelector('.container').scrollIntoView({ behavior: 'smooth' });
    
    // Restaurar el texto del botón después de actualizar
    const handleSubmit = () => {
        submitBtn.textContent = originalText;
        submitBtn.removeEventListener('click', handleSubmit);
        saveInsumosToStorage();
        renderTable();
    };
    
    submitBtn.addEventListener('click', handleSubmit, { once: true });
}

// Eliminar insumo
function deleteInsumo(index) {
    if (confirm(`¿Estás seguro de que deseas eliminar "${insumos[index].nombre_insumo}"?`)) {
        insumos.splice(index, 1);
        saveInsumosToStorage();
        renderTable();
        alert('Insumo eliminado correctamente');
    }
}

// Manejar el envío del formulario
document.getElementById("insumosForm")?.addEventListener("submit", function(e) {
    e.preventDefault();
    
    // Validar campos requeridos
    if (!this.nombre_insumo.value || !this.unidad.value || !this.cantidad.value || !this.costo.value) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }
    
    const insumoData = {
        nombre_insumo: this.nombre_insumo.value,
        unidad: this.unidad.value,
        cantidad: this.cantidad.value,
        costo: this.costo.value,
        proveedor: this.proveedor.value,
        stock_minimo: this.stock_minimo.value,
        descripcion: this.descripcion.value
    };
    
    // Agregar a la tabla local
    addInsumo(insumoData);
    
    // Limpiar formulario
    this.reset();
    
    alert('Insumo agregado exitosamente');
});

// Event listeners para filtros
document.getElementById('searchInput')?.addEventListener('input', () => renderTable());
document.getElementById('filterUnidad')?.addEventListener('change', () => renderTable());

// Función para exportar a CSV
function exportInsumosToCSV() {
    if (insumos.length === 0) {
        alert('No hay insumos para exportar');
        return;
    }
    
    const headers = ['Nombre', 'Unidad', 'Cantidad', 'Costo Unitario', 'Valor Total', 'Proveedor', 'Stock Mínimo', 'Descripción'];
    const csvRows = [];
    csvRows.push(headers.join(','));
    
    for (const insumo of insumos) {
        const valorTotal = insumo.cantidad * insumo.costo;
        const row = [
            `"${insumo.nombre_insumo}"`,
            `"${insumo.unidad}"`,
            insumo.cantidad,
            insumo.costo,
            valorTotal.toFixed(2),
            `"${insumo.proveedor || ''}"`,
            insumo.stock_minimo || 0,
            `"${insumo.descripcion || ''}"`
        ];
        csvRows.push(row.join(','));
    }
    
    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'insumos.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Agregar botón de exportar
const exportBtn = document.createElement('button');
exportBtn.textContent = '📥 EXPORTAR CSV';
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
exportBtn.onclick = exportInsumosToCSV;
document.body.appendChild(exportBtn);

// Cargar insumos al iniciar
loadInsumosFromStorage();