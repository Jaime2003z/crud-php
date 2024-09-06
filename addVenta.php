<?php
include("./config.php");

// Verifica la conexión
if ($db->connect_error) {
    die("La conexión falló: " . $db->connect_error);
}

// Obtener datos del formulario
$codigoProducto = $_POST['codigoProducto'];
$tipoCliente = $_POST['selectCliente'];
$nombreCliente = $_POST['nombreCliente'];
$idCliente = $_POST['idCliente'];
$productos = $_POST['productos'];
$cantidades = $_POST['cantidades']; 

// Preparar información del cliente según tipo
if ($tipoCliente === 'Empresa') {
    $infoCliente = "Razón Social: $nombreCliente\nNIT: $idCliente\n";
} else {
    $infoCliente = "Nombre: $nombreCliente\nCédula: $idCliente\n";
}

$totalVenta = 0;

// Iniciar una transacción
$db->begin_transaction();

try {
    // Insertar la venta en la tabla Venta
    $queryVenta = "INSERT INTO Venta (fecha_venta, total_venta) VALUES (NOW(), $totalVenta)";
    $stmtVenta = $db->prepare($queryVenta);
    $stmtVenta->execute();
    $idVenta = $db->insert_id;

    // Verificar si el cliente ya existe
    $queryCliente = "SELECT id_cliente FROM cliente WHERE cedula_nit = $idCliente";
    $stmtCliente = $db->prepare($queryCliente);
    $stmtCliente->execute();
    $resultCliente = $stmtCliente->get_result();

    if ($resultCliente->num_rows == 0) {
        // Si el cliente no existe, insertarlo
        $queryInsertCliente = "INSERT INTO cliente (nombre_cliente, cedula_nit, tipo_cliente) VALUES ('$nombreCliente', '$idCliente', '$tipoCliente')";
        $stmtInsertCliente = $db->prepare($queryInsertCliente);
        $stmtInsertCliente->execute();

        // Obtener el ID del cliente recién insertado
        $idCliente = $db->insert_id;
    } else {
    // Si el cliente ya existe, obtener su id_cliente
        $rowCliente = $resultCliente->fetch_assoc();
        $idCliente = $rowCliente['id_cliente'];
    }


    // Iterar sobre los productos para registrar los detalles de la venta
    for ($i = 0; $i < count($productos); $i++) {
        $codigoProducto = $productos[$i];
        $cantidadVendida = $cantidades[$i];

        // Consultar detalles del producto
        $queryProducto = "SELECT nombre_producto, precio, cantidad_disponible FROM producto WHERE codigo_producto = $codigoProducto";
        $stmtProducto = $db->prepare($queryProducto);
        $stmtProducto->execute();
        $resultProducto = $stmtProducto->get_result();
        $producto = $resultProducto->fetch_assoc();

        $nombreProducto = $producto['nombre_producto'];
        $precio = $producto['precio'];
        $subtotal = $precio * $cantidadVendida;
        $totalVenta += $subtotal;

        // Verificar si hay suficiente cantidad disponible
        if ($producto['cantidad_disponible'] < $cantidadVendida) {
            throw new Exception("No hay suficiente stock para el producto $nombreProducto. Solo hay $cantidadVendida");
        }

        // Registrar el detalle de la venta
        $queryDetalle = "INSERT INTO DetalleVenta (fk_id_venta, fk_codigo_producto, cantidad_vendida, subtotal) VALUES ( '$idVenta', '$codigoProducto', '$cantidadVendida', '$subtotal')";
        $stmtDetalle = $db->prepare($queryDetalle);
        $stmtDetalle->execute();

        // Actualizar la cantidad disponible del producto
        $nuevaCantidad = $producto['cantidad_disponible'] - $cantidadVendida;
        $queryUpdateProducto = "UPDATE producto SET cantidad_disponible = $nuevaCantidad WHERE codigo_producto = $codigoProducto";
        $stmtUpdateProducto = $db->prepare($queryUpdateProducto);
        $stmtUpdateProducto->execute();
    }

    // Actualizar el total de la venta
    $queryUpdateVenta = "UPDATE Venta SET total_venta =  $totalVenta WHERE id_venta = $idVenta";
    $stmtUpdateVenta = $db->prepare($queryUpdateVenta);
    $stmtUpdateVenta->execute();

    // Insertar la factura en la tabla factura
    $queryFactura = "INSERT INTO factura (fk_id_venta, fk_id_cliente) VALUES ($idVenta, $idCliente)";
    $stmtFactura = $db->prepare($queryFactura);
    $stmtFactura->execute();

    // Confirmar la transacción
    $db->commit();

    // Crear el archivo de recibo
    $nombreArchivo = "recibo_" . time() . ".txt";  
    $rutaArchivo = "recibos/" . $nombreArchivo;   
    $archivo = fopen($rutaArchivo, "w");

    // Escribir información del cliente
    fwrite($archivo, "RECIBO DE VENTA\n");
    fwrite($archivo, "-----------------------------\n");
    fwrite($archivo, $infoCliente);
    fwrite($archivo, "-----------------------------\n");

    // Escribir información de productos
    fwrite($archivo, "Productos:\n");

    for ($i = 0; $i < count($productos); $i++) {
        $codigoProducto = $productos[$i];
        $cantidad = $cantidades[$i];

        // Consultar detalles del producto
        $query = "SELECT nombre_producto, precio FROM producto WHERE codigo_producto = '$codigoProducto'";
        $result = $db->query($query);
        $producto = $result->fetch_assoc();

        $nombreProducto = $producto['nombre_producto'];
        $precio = $producto['precio'];
        $subtotal = $precio * $cantidad;

        // Escribir detalles del producto en el recibo
        fwrite($archivo, "$nombreProducto (Codigo: $codigoProducto)\n");
        fwrite($archivo, "Cantidad: $cantidad\n");
        fwrite($archivo, "Precio: $precio\n");
        fwrite($archivo, "Subtotal: $subtotal\n");
        fwrite($archivo, "-----------------------------\n");
    }

    // Escribir el total
    fwrite($archivo, "Total a pagar: $totalVenta\n");

    // Cerrar el archivo
    fclose($archivo);

    // Mensaje de éxito
    //echo "Venta registrada y recibo generado: <a href='$rutaArchivo'>Descargar recibo</a>";

    if ($query) {
        $rutaArchivo = urlencode($rutaArchivo);
        header('Location: ventas.php?venta=exito&rutaArchivo=' . $rutaArchivo);
    } else
        die('Location: ./ventas.php?venta=error');
    
} catch (Exception $e) {
    $db->rollback();
    echo "Error: " . $e->getMessage();
}

// Cerrar la conexión
$db->close();
?>