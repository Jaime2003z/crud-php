<?php
include("./config.php");

if (isset($_POST['addProduct'])) {
    $codigo = $_POST['codeProducto'];
    $nombre = $_POST['nmbreProducto'];
    $precio = $_POST['priceProducto'];
    $cantidad = $_POST['cantidadProducto'];

    // query
    $sql = "INSERT INTO Producto(codigo_producto, nombre_producto, precio, cantidad_disponible)
    VALUES('$codigo', '$nombre', '$precio', '$cantidad')";
    $query = mysqli_query($db, $sql);

    if ($query)
        header('Location: ./index.php?status=exito');
    else
        header('Location: ./index.php?status=error');
} else
    die("Acceso prohibido...");
