<?php
require_once 'config.php';

header('Content-Type: application/json');

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch($accion) {
    case 'cargarInsumos':
        cargarInsumos($conn);
        break;
    case 'guardarInsumo':
        guardarInsumo($conn);
        break;
    case 'eliminarInsumo':
        eliminarInsumo($conn);
        break;
    case 'obtenerInsumo':
        obtenerInsumo($conn);
        break;
    case 'cargarProductos':
        cargarProductos($conn);
        break;
    case 'guardarProducto':
        guardarProducto($conn);
        break;
    case 'eliminarProducto':
        eliminarProducto($conn);
        break;
    case 'obtenerProducto':
        obtenerProducto($conn);
        break;
    case 'cargarInsumosSelect':
        cargarInsumosSelect($conn);
        break;
}

// Funciones para Insumos
function cargarInsumos($conn) {
    $buscar = $_GET['buscar'] ?? '';
    
    if($buscar) {
        $sql = "SELECT * FROM insumos WHERE nombre LIKE ? OR Descripcion LIKE ?";
        $stmt = $conn->prepare($sql);
        $param = "%$buscar%";
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $resultado = $stmt->get_result();
    } else {
        $sql = "SELECT * FROM insumos ORDER BY Id_insumos DESC";
        $resultado = $conn->query($sql);
    }
    
    $insumos = [];
    while($fila = $resultado->fetch_assoc()) {
        $insumos[] = $fila;
    }
    
    echo json_encode(['success' => true, 'data' => $insumos]);
}

function guardarInsumo($conn) {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $unidad_medida = $_POST['unidad_medida'];
    $peso_unitario = $_POST['peso_unitario'];
    $valor_unitario = $_POST['valor_unitario'];
    
    if($id) {
        // Actualizar
        $sql = "UPDATE insumos SET nombre=?, Descripcion=?, Unidad_medida=?, Peso_unitario=?, Valor_unitario=? WHERE Id_insumos=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdddi", $nombre, $descripcion, $unidad_medida, $peso_unitario, $valor_unitario, $id);
    } else {
        // Insertar nuevo
        $sql = "INSERT INTO insumos (nombre, Descripcion, Unidad_medida, Peso_unitario, Valor_unitario) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddd", $nombre, $descripcion, $unidad_medida, $peso_unitario, $valor_unitario);
    }
    
    if($stmt->execute()) {
        $mensaje = $id ? 'Insumo actualizado exitosamente' : 'Insumo creado exitosamente';
        echo json_encode(['success' => true, 'message' => $mensaje]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar insumo: ' . $conn->error]);
    }
}

function eliminarInsumo($conn) {
    $id = $_POST['id'];
    
    // Verificar si hay productos que usen este insumo
    $sql_check = "SELECT COUNT(*) as total FROM producto_terminado WHERE Id_Insumos=?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $id);
    $stmt_check->execute();
    $resultado = $stmt_check->get_result();
    $row = $resultado->fetch_assoc();
    
    if($row['total'] > 0) {
        echo json_encode(['success' => false, 'message' => 'No se puede eliminar: Hay productos que dependen de este insumo']);
        return;
    }
    
    $sql = "DELETE FROM insumos WHERE Id_insumos=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Insumo eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar insumo: ' . $conn->error]);
    }
}

function obtenerInsumo($conn) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM insumos WHERE Id_insumos=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if($fila = $resultado->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $fila]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Insumo no encontrado']);
    }
}

// Funciones para Productos Terminados
function cargarProductos($conn) {
    $buscar = $_GET['buscar'] ?? '';
    
    if($buscar) {
        $sql = "SELECT pt.*, i.nombre as nombre_insumo 
                FROM producto_terminado pt 
                INNER JOIN insumos i ON pt.Id_Insumos = i.Id_insumos 
                WHERE pt.nombre LIKE ? OR pt.Descripcion LIKE ?";
        $stmt = $conn->prepare($sql);
        $param = "%$buscar%";
        $stmt->bind_param("ss", $param, $param);
        $stmt->execute();
        $resultado = $stmt->get_result();
    } else {
        $sql = "SELECT pt.*, i.nombre as nombre_insumo 
                FROM producto_terminado pt 
                INNER JOIN insumos i ON pt.Id_Insumos = i.Id_insumos 
                ORDER BY pt.Id_ProductoTerminado DESC";
        $resultado = $conn->query($sql);
    }
    
    $productos = [];
    while($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
    
    echo json_encode(['success' => true, 'data' => $productos]);
}

function guardarProducto($conn) {
    $id = $_POST['id'] ?? null;
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $unidad_medida = $_POST['unidad_medida'];
    $peso_unitario = $_POST['peso_unitario'];
    $valor_unitario = $_POST['valor_unitario'];
    $id_insumos = $_POST['id_insumos'];
    
    if($id) {
        // Actualizar
        $sql = "UPDATE producto_terminado SET nombre=?, Descripcion=?, Unidad_medida=?, Peso_unitario=?, Valor_unitario=?, Id_Insumos=? WHERE Id_ProductoTerminado=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdddii", $nombre, $descripcion, $unidad_medida, $peso_unitario, $valor_unitario, $id_insumos, $id);
    } else {
        // Insertar nuevo
        $sql = "INSERT INTO producto_terminado (nombre, Descripcion, Unidad_medida, Peso_unitario, Valor_unitario, Id_Insumos) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdddi", $nombre, $descripcion, $unidad_medida, $peso_unitario, $valor_unitario, $id_insumos);
    }
    
    if($stmt->execute()) {
        $mensaje = $id ? 'Producto actualizado exitosamente' : 'Producto creado exitosamente';
        echo json_encode(['success' => true, 'message' => $mensaje]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar producto: ' . $conn->error]);
    }
}

function eliminarProducto($conn) {
    $id = $_POST['id'];
    $sql = "DELETE FROM producto_terminado WHERE Id_ProductoTerminado=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    
    if($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Producto eliminado exitosamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar producto: ' . $conn->error]);
    }
}

function obtenerProducto($conn) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM producto_terminado WHERE Id_ProductoTerminado=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if($fila = $resultado->fetch_assoc()) {
        echo json_encode(['success' => true, 'data' => $fila]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    }
}

function cargarInsumosSelect($conn) {
    $sql = "SELECT Id_insumos, nombre FROM insumos ORDER BY nombre";
    $resultado = $conn->query($sql);
    
    $insumos = [];
    while($fila = $resultado->fetch_assoc()) {
        $insumos[] = $fila;
    }
    
    echo json_encode(['success' => true, 'data' => $insumos]);
}

$conn->close();
?>