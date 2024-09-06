<?php include("./config.php"); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Belajar Dasar CRUD dengan PHP dan MySQL">
    <title>CRUD con PHP</title>

    <!-- bootstrap cdn -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">

    <!-- material icon -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- font awesome -->
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css"
        integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous" />

    <link rel="stylesheet" href="./css/style.css">
</head>

<body class="bg-light">
    <style>
    #totalRow {
        font-weight: bold;
    }
    </style>

    <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" style="position: sticky !important;
    top: 0 !important; z-index : 99999 !important;">
        <div class="container-fluid container">
            <h3>CRUD con PHP hecho por Jaime2003z</h3>
            <a class="btn btn-primary" href="./index.php" role="button">productos</a>
        </div>
    </nav>


    <div class="container mt-5">
        <div class="card mb-5">
            <div class="card-body">
                <h3 class="card-title">Registro de ventas</h3>

                <?php if (isset($_GET['venta'])) : ?>
                <?php
                if ($_GET['venta'] == 'exito') {
                    $rutaArchivo = $_GET['rutaArchivo'];
                    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>exito!</strong> Venta registrada y recibo generado: <a href='$rutaArchivo'>Descargar recibo</a>
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                }
                else
                    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Ups!</strong> Error eliminando los datos!
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                    </div>";
                ?>

                <?php endif; ?>
                <?php 
                $query = "SELECT codigo_producto, nombre_producto, precio FROM producto";
                $result = $db->query($query);
                ?>

                <form method="POST" action="addVenta.php">
                    <div class="row g-3 d-flex align-items-end mb-3 mt-3">
                        <div class="col-md-3">
                            <label for="producto">Selecciona un producto:</label>
                            <select id="producto" class="form-select mt-1" name="codigoProducto">
                                <?php while ($row = $result->fetch_assoc()): ?>
                                <option value="<?= $row['codigo_producto'] ?>" data-precio="<?= $row['precio'] ?>">
                                    <?= $row['nombre_producto'] ?>
                                    (<?= $row['precio'] ?>)</option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <input type="number" class="form-control" id="cantidad" name="cantidad"
                                placeholder="Cantidad" min="1">
                        </div>
                        <div class="col-md-3">
                            <button type="button" class="btn btn-primary" onclick="agregarProducto()">Añadir
                                producto</button>
                        </div>
                    </div>

                    <div class="col-12 mb-3 ">
                        <label for="selectCliente" class="form-label">Tipo cliente</label>
                        <select class="form-select" name="selectCliente" id="selectCliente"
                            aria-label="Default select example" onchange="cambiarCampos()">
                            <option value="Natural">Natural</option>
                            <option value="Empresa">Empresa</option>
                        </select>
                    </div>

                    <div class="row g-3 mb-3 mt-3">
                        <div class="col-md-6 mb-3">
                            <label for="nombreCliente" id="labelNombre" class="form-label">Nombre</label>
                            <input type="text" name="nombreCliente" id="nombreCliente" class="form-control"
                                placeholder="Fulanito" required>
                        </div>

                        <div class="col-md-6">
                            <label for="idCliente" id="labelId" class="form-label">Cédula</label>
                            <input type="number" name="idCliente" id="idCliente" class="form-control" min="0"
                                placeholder="100029443" required>
                        </div>
                    </div>
                    <table id="productos_seleccionados" class="mt-3 table table-hover align-middle bg-white">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Aquí se mostrarán los productos seleccionados -->
                        </tbody>
                        <tfoot>
                            <tr id="totalRow">
                                <td colspan="3">Total a pagar:</td>
                                <td id="total">0</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>

                    <button type="reset" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i><span class="ms-2">Limpiar</span>
                        </button>
                    <input type="submit" class="btn btn-primary" value="Registrar Venta">
                </form>

                <script>
                let totalPagar = 0;

                function cambiarCampos() {
                    let tipoCliente = document.getElementById('selectCliente').value;
                    let labelNombre = document.getElementById('labelNombre');
                    let inputNombre = document.getElementById('nombreCliente');
                    let labelId = document.getElementById('labelId');
                    let inputId = document.getElementById('idCliente');

                    if (tipoCliente === 'Empresa') {
                        // Cambiar etiquetas y placeholders para empresa
                        labelNombre.innerText = 'Razón Social';
                        inputNombre.placeholder = 'Nombre de la Empresa';
                        labelId.innerText = 'NIT';
                        inputId.placeholder = '900123456';
                    } else {
                        // Cambiar etiquetas y placeholders para persona natural
                        labelNombre.innerText = 'Nombre';
                        inputNombre.placeholder = 'Fulanito';
                        labelId.innerText = 'Cédula';
                        inputId.placeholder = '100029443';
                    }
                }

                function agregarProducto() {
                    // Obtiene los valores del producto y la cantidad seleccionada
                    let productoSelect = document.getElementById('producto');
                    let producto = productoSelect.options[productoSelect.selectedIndex].text;
                    let productoId = productoSelect.value;
                    let precio = productoSelect.options[productoSelect.selectedIndex].getAttribute('data-precio');
                    let cantidad = document.getElementById('cantidad').value;

                    // Valida que se haya seleccionado un producto y una cantidad
                    if (productoId && cantidad > 0) {
                        let subtotal = cantidad * precio;
                        totalPagar += subtotal;

                        // Inserta una nueva fila en la tabla con el producto, cantidad, precio y subtotal
                        let tabla = document.getElementById('productos_seleccionados').getElementsByTagName('tbody')[0];
                        let nuevaFila = tabla.insertRow();

                        nuevaFila.innerHTML = `
            <td><input type="hidden" name="productos[]" value="${productoId}">${producto}</td>
            <td><input type="hidden" name="cantidades[]" value="${cantidad}">${cantidad}</td>
            <td>${precio}</td>
            <td>${subtotal}</td>
            <td><button type="button" onclick="eliminarFila(this, ${subtotal})" class='btn btn-danger deleteButton pad m-1'><span class='material-icons align-middle'>delete</span></button></td>
        `;

                        // Actualiza el total
                        document.getElementById('total').innerText = totalPagar.toFixed(2);
                    }
                }

                function eliminarFila(boton, subtotal) {
                    // Elimina la fila donde se encuentra el botón de eliminar
                    let fila = boton.closest('tr');
                    fila.remove();

                    // Resta el subtotal del producto eliminado al total
                    totalPagar -= subtotal;
                    document.getElementById('total').innerText = totalPagar.toFixed(2);
                }
                </script>

            </div>
        </div>

        <!-- Javascript bule with popper bootstrap -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
        </script>

        <!-- sweet alert -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</body>

</html>