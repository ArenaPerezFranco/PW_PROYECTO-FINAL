let tabActual = 'insumos';

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarInsumos();
    cargarProductos();
    cargarInsumosSelect();
});

// Cambiar entre pestañas
function cambiarTab(tab) {
    tabActual = tab;
    
    // Actualizar botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
    
    // Actualizar contenido
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`tab-${tab}`).classList.add('active');
    
    // Actualizar tablas visibles
    document.querySelectorAll('.tabla-content').forEach(content => {
        content.classList.remove('active');
    });
    document.getElementById(`tabla-${tab}`).classList.add('active');
}

// ============ FUNCIONES PARA INSUMOS ============

function cargarInsumos() {
    const buscar = document.getElementById('buscarInsumo').value;
    
    fetch(`acciones.php?accion=cargarInsumos&buscar=${buscar}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const tbody = document.getElementById('tbodyInsumos');
                tbody.innerHTML = '';
                
                data.data.forEach(insumo => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${insumo.Id_insumos}</td>
                            <td>${insumo.nombre}</td>
                            <td>${insumo.Descripcion}</td>
                            <td>${insumo.Unidad_medida}</td>
                            <td>${insumo.Peso_unitario}</td>
                            <td>${insumo.Valor_unitario}</td>
                            <td>${insumo.Id_FraccionYnico}</td>
                            <td>${insumo.Id_exportacion}</td>
                            <td>
                                <button class="btn btn-info" onclick="editarInsumo(${insumo.Id_insumos})">Editar</button>
                                <button class="btn btn-danger" onclick="eliminarInsumo(${insumo.Id_insumos})">Eliminar</button>
                            </td>
                        </tr>
                    `;
                });
            }
        });
}

function guardarInsumo(event) {
    event.preventDefault();
    
    const id = document.getElementById('idInsumo').value;
    const formData = new FormData();
    formData.append('accion', 'guardarInsumo');
    formData.append('id', id);
    formData.append('nombre', document.getElementById('nombreInsumo').value);
    formData.append('descripcion', document.getElementById('descripcionInsumo').value);
    formData.append('unidad_medida', document.getElementById('unidadMedidaInsumo').value);
    formData.append('peso_unitario', document.getElementById('pesoUnitarioInsumo').value);
    formData.append('valor_unitario', document.getElementById('valorUnitarioInsumo').value);
    formData.append('id_fraccion_ynico', document.getElementById('idFraccionYnico').value);
    formData.append('id_exportacion', document.getElementById('idExportacion').value);
    
    fetch('acciones.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            limpiarFormulario('insumos');
            cargarInsumos();
        } else {
            alert(data.message);
        }
    });
}

function editarInsumo(id) {
    fetch(`acciones.php?accion=obtenerInsumo&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const insumo = data.data;
                document.getElementById('idInsumo').value = insumo.Id_insumos;
                document.getElementById('nombreInsumo').value = insumo.nombre;
                document.getElementById('descripcionInsumo').value = insumo.Descripcion;
                document.getElementById('unidadMedidaInsumo').value = insumo.Unidad_medida;
                document.getElementById('pesoUnitarioInsumo').value = insumo.Peso_unitario;
                document.getElementById('valorUnitarioInsumo').value = insumo.Valor_unitario;
                document.getElementById('idFraccionYnico').value = insumo.Id_FraccionYnico;
                document.getElementById('idExportacion').value = insumo.Id_exportacion;
                
                // Scroll al formulario
                document.getElementById('tab-insumos').scrollIntoView({behavior: 'smooth'});
            }
        });
}

function eliminarInsumo(id) {
    if(confirm('¿Está seguro de eliminar este insumo?')) {
        const formData = new FormData();
        formData.append('accion', 'eliminarInsumo');
        formData.append('id', id);
        
        fetch('acciones.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                cargarInsumos();
            } else {
                alert(data.message);
            }
        });
    }
}

// ============ FUNCIONES PARA PRODUCTOS ============

function cargarProductos() {
    const buscar = document.getElementById('buscarProducto').value;
    
    fetch(`acciones.php?accion=cargarProductos&buscar=${buscar}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const tbody = document.getElementById('tbodyProductos');
                tbody.innerHTML = '';
                
                data.data.forEach(producto => {
                    tbody.innerHTML += `
                        <tr>
                            <td>${producto.Id_ProductoTerminado}</td>
                            <td>${producto.nombre}</td>
                            <td>${producto.Descripcion}</td>
                            <td>${producto.Unidad_medida}</td>
                            <td>${producto.Peso_unitario}</td>
                            <td>${producto.Valor_unitario}</td>
                            <td>${producto.Id_Insumos} - ${producto.nombre_insumo}</td>
                            <td>
                                <button class="btn btn-info" onclick="editarProducto(${producto.Id_ProductoTerminado})">Editar</button>
                                <button class="btn btn-danger" onclick="eliminarProducto(${producto.Id_ProductoTerminado})">Eliminar</button>
                            </td>
                        </tr>
                    `;
                });
            }
        });
}

function guardarProducto(event) {
    event.preventDefault();
    
    const id = document.getElementById('idProducto').value;
    const formData = new FormData();
    formData.append('accion', 'guardarProducto');
    formData.append('id', id);
    formData.append('nombre', document.getElementById('nombreProducto').value);
    formData.append('descripcion', document.getElementById('descripcionProducto').value);
    formData.append('unidad_medida', document.getElementById('unidadMedidaProducto').value);
    formData.append('peso_unitario', document.getElementById('pesoUnitarioProducto').value);
    formData.append('valor_unitario', document.getElementById('valorUnitarioProducto').value);
    formData.append('id_insumos', document.getElementById('idInsumoFK').value);
    
    fetch('acciones.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            alert(data.message);
            limpiarFormulario('productos');
            cargarProductos();
        } else {
            alert(data.message);
        }
    });
}

function editarProducto(id) {
    fetch(`acciones.php?accion=obtenerProducto&id=${id}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const producto = data.data;
                document.getElementById('idProducto').value = producto.Id_ProductoTerminado;
                document.getElementById('nombreProducto').value = producto.nombre;
                document.getElementById('descripcionProducto').value = producto.Descripcion;
                document.getElementById('unidadMedidaProducto').value = producto.Unidad_medida;
                document.getElementById('pesoUnitarioProducto').value = producto.Peso_unitario;
                document.getElementById('valorUnitarioProducto').value = producto.Valor_unitario;
                document.getElementById('idInsumoFK').value = producto.Id_Insumos;
                
                // Scroll al formulario
                document.getElementById('tab-productos').scrollIntoView({behavior: 'smooth'});
            }
        });
}

function eliminarProducto(id) {
    if(confirm('¿Está seguro de eliminar este producto?')) {
        const formData = new FormData();
        formData.append('accion', 'eliminarProducto');
        formData.append('id', id);
        
        fetch('acciones.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                alert(data.message);
                cargarProductos();
            } else {
                alert(data.message);
            }
        });
    }
}

function cargarInsumosSelect() {
    fetch('acciones.php?accion=cargarInsumosSelect')
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                const select = document.getElementById('idInsumoFK');
                select.innerHTML = '<option value="">Seleccionar insumo</option>';
                
                data.data.forEach(insumo => {
                    select.innerHTML += `<option value="${insumo.Id_insumos}">${insumo.nombre}</option>`;
                });
            }
        });
}

function limpiarFormulario(tipo) {
    if(tipo === 'insumos') {
        document.getElementById('idInsumo').value = '';
        document.getElementById('formInsumos').reset();
    } else {
        document.getElementById('idProducto').value = '';
        document.getElementById('formProductos').reset();
    }
}