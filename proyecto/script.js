// Array para almacenar productos
let productos = [];

// Cargar productos del localStorage al iniciar
function loadProductsFromStorage() {
    const stored = localStorage.getItem('productos');
    if (stored) {
        productos = JSON.parse(stored);
        renderTable();
    }
}

// Guardar productos en localStorage
function saveProductsToStorage() {
    localStorage.setItem('productos', JSON.stringify(productos));
}

// Renderizar la tabla
function renderTable() {
    const tbody = document.getElementById('tableBody');
    
    if (productos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="6" class="empty-message">No hay productos registrados</td></tr>';
        return;
    }
    
    tbody.innerHTML = productos.map((producto, index) => `
        <tr>
            <td>${escapeHtml(producto.nombre)}</td>
            <td>${escapeHtml(producto.unidad)}</td>
            <td>${parseFloat(producto.peso).toFixed(2)} kg</td>
            <td>$${parseFloat(producto.valor).toFixed(2)}</td>
            <td>${escapeHtml(producto.descripcion) || '-'}</td>
            <td>
                <button class="btn-edit" onclick="editProduct(${index})">✏️ Editar</button>
                <button class="btn-delete" onclick="deleteProduct(${index})">🗑️ Eliminar</button>
            </td>
        </tr>
    `).join('');
}

// Función para escapar HTML y evitar XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Agregar nuevo producto
function addProduct(productData) {
    const newProduct = {
        nombre: productData.nombre,
        unidad: productData.unidad,
        peso: parseFloat(productData.peso),
        valor: parseFloat(productData.valor),
        descripcion: productData.descripcion || ''
    };
    
    productos.push(newProduct);
    saveProductsToStorage();
    renderTable();
    return true;
}

// Editar producto
function editProduct(index) {
    const producto = productos[index];
    
    // Llenar el formulario con los datos del producto
    document.querySelector('input[name="nombre"]').value = producto.nombre;
    document.querySelector('input[name="unidad"]').value = producto.unidad;
    document.querySelector('input[name="peso"]').value = producto.peso;
    document.querySelector('input[name="valor"]').value = producto.valor;
    document.querySelector('textarea[name="descripcion"]').value = producto.descripcion;
    
    // Cambiar el texto del botón temporalmente
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.textContent;
    submitBtn.textContent = 'ACTUALIZAR PRODUCTO';
    
    // Eliminar el producto actual y preparar para actualización
    productos.splice(index, 1);
    
    // Scroll suave al formulario
    document.querySelector('.container').scrollIntoView({ behavior: 'smooth' });
    
    // Restaurar el texto del botón después de actualizar
    const handleSubmit = () => {
        submitBtn.textContent = originalText;
        submitBtn.removeEventListener('click', handleSubmit);
    };
    
    submitBtn.addEventListener('click', handleSubmit, { once: true });
}

// Eliminar producto
function deleteProduct(index) {
    if (confirm(`¿Estás seguro de que deseas eliminar "${productos[index].nombre}"?`)) {
        productos.splice(index, 1);
        saveProductsToStorage();
        renderTable();
        alert('Producto eliminado correctamente');
    }
}

// Manejar el envío del formulario
document.getElementById("productoForm").addEventListener("submit", async function(e) {
    e.preventDefault();
    
    // Validar campos requeridos
    if (!this.nombre.value || !this.unidad.value || !this.peso.value || !this.valor.value) {
        alert('Por favor complete todos los campos obligatorios');
        return;
    }
    
    const productData = {
        nombre: this.nombre.value,
        unidad: this.unidad.value,
        peso: this.peso.value,
        valor: this.valor.value,
        descripcion: this.descripcion.value
    };
    
    // Agregar a la tabla local
    addProduct(productData);
    
    // Limpiar formulario
    this.reset();
    
    alert('Producto agregado exitosamente');
    
    // Opcional: Enviar al servidor
    /* try {
        const response = await fetch("guardar.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(productData)
        });
        
        const result = await response.text();
        console.log("Respuesta del servidor:", result);
    } catch(error) {
        console.error("Error al guardar en servidor:", error);
    } */
});

// Cargar productos al iniciar
loadProductsFromStorage();

// Función para exportar datos (opcional)
function exportToCSV() {
    if (productos.length === 0) {
        alert('No hay productos para exportar');
        return;
    }
    
    const headers = ['Nombre', 'Unidad', 'Peso (kg)', 'Valor ($)', 'Descripción'];
    const csvRows = [];
    csvRows.push(headers.join(','));
    
    for (const producto of productos) {
        const row = [
            `"${producto.nombre}"`,
            `"${producto.unidad}"`,
            producto.peso,
            producto.valor,
            `"${producto.descripcion || ''}"`
        ];
        csvRows.push(row.join(','));
    }
    
    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'productos.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
}

// Agregar botón de exportar (opcional)
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
`;
exportBtn.onclick = exportToCSV;
document.body.appendChild(exportBtn);