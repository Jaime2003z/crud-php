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
    <!-- navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom" style="position: sticky !important;
    top: 0 !important; z-index : 99999 !important;">
        <div class="container-fluid container">
            <h3>CRUD con PHP hecho por Jaime2003z</h3>
            <a class="btn btn-primary" href="./ventas.php" role="button">ventas</a>
    </nav>


    <div class="container mt-5">
        <div class="card mb-5">
            <div class="card-body">
                <h3 class="card-title">Registro de productos</h3>
                <?php if (isset($_GET['status'])) : ?>
                <?php
                    if ($_GET['status'] == 'exito')
                        echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>exito!</strong> 
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
                    else
                        echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Ups!</strong>
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
                    ?>
                <?php endif; ?>


                <form class="row g-3" action="addProduct.php" method="POST">

                    <div class="col-md-6">
                        <label for="codeProducto" class="form-label">Codigo producto:</label>
                        <input type="number" name="codeProducto" class="form-control" placeholder="64190021" min="0"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label for="nmbreProducto" class="form-label">Nombre producto</label>
                        <input type="text" name="nmbreProducto" class="form-control" placeholder="Laptop" required>
                    </div>

                    <div class="col-md-6">
                        <label for="priceProducto" class="form-label">Precio producto</label>
                        <input type="number" name="priceProducto" class="form-control" placeholder="150000" min="0"
                            required>
                    </div>

                    <div class="col-md-6">
                        <label for="cantidadProducto" class="form-label">Cantidad producto</label>
                        <input type="number" name="cantidadProducto" class="form-control" placeholder="10" min="0"
                            required>
                    </div>
                    <div class="col-12">
                        <button type="reset" class="btn btn-secondary">
                            <i class="fa fa-refresh"></i><span class="ms-2">Limpiar</span>
                        </button>
                        <button type="submit" class="btn btn-primary" value="daftar" name="addProduct"><i
                                class="fa fa-plus"></i><span class="ms-2">Agregar</span></button>
                    </div>
                </form>
            </div>
        </div>


        <h3 class="mb-3">Visualización productos</h3>

        <?php if (isset($_GET['hapus'])) : ?>
        <?php
            if ($_GET['hapus'] == 'exito')
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>exito!</strong> Datos eliminados exitosamente!
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            else
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Ups!</strong> Error eliminando los datos!
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            ?>
        <?php endif; ?>

        <?php if (isset($_GET['update'])) : ?>
        <?php
            if ($_GET['update'] == 'exito')
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                        <strong>exito!</strong> Datos actualizados exitosamente!
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            else
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                        <strong>Ups!</strong> Error actualizando los datos!
                        <button type='button' class='btn-close' onclick='clicking()' data-bs-dismiss='alert' aria-label='Close'></button>
                      </div>";
            ?>
        <?php endif; ?>

        <!-- tabel -->
        <div class="table-responsive mb-5 card">
            <?php
            echo "<div class='card-body'>";

            echo "<table class='table table-hover align-middle bg-white'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th scope='col' class='text-center'>Codigo</th>";
            echo "<th scope='col'>Nombre</th>";
            echo "<th scope='col'>Precio</th>";
            echo "<th scope='col'>Cantidad</th>";
            echo "<th scope='col'>Estado</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";


    // Número de productos por página
    $productos_por_pagina = 10;

    // Página actual
    $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

    // Cálculo del índice del primer producto a mostrar
    $indice_inicial = ($pagina_actual > 1) ? ($pagina_actual * $productos_por_pagina) - $productos_por_pagina : 0;

    // Página anterior y siguiente
    $pagina_anterior = $pagina_actual - 1;
    $pagina_siguiente = $pagina_actual + 1;

    // Consulta para contar el total de productos
    $consulta_total_productos = mysqli_query($db, "SELECT * FROM Producto");
    $total_productos = mysqli_num_rows($consulta_total_productos);

    // Cálculo del total de páginas
    $total_paginas = ceil($total_productos / $productos_por_pagina);

    // Consulta para obtener los productos de la página actual
    $consulta_productos = mysqli_query($db, "SELECT * FROM Producto LIMIT $indice_inicial, $productos_por_pagina");

    // Número inicial para la lista de productos
    $numero_producto = $indice_inicial + 1;

            while ($producto  = mysqli_fetch_array($consulta_productos)) {
                echo "<tr>";
                echo "<td>" . $producto['codigo_producto'] . "</td>";
                echo "<td>" . $producto['nombre_producto'] . "</td>";
                echo "<td>" . $producto['precio'] . "</td>";
                echo "<td>" . $producto['cantidad_disponible'] . "</td>";
                if ($producto['cantidad_disponible'] == 0) {
                    echo "<td>Agotado</td>";
                } else {
                    echo "<td>En stock</td>";
                }

                echo "<td class='text-center'>";

                echo "<button type='button' class='btn btn-primary editButton pad m-1'><span class='material-icons align-middle'>edit</span></button>";

                // tombol hapus
                echo "
                            <!-- Button trigger modal -->
                            <button type='button' class='btn btn-danger deleteButton pad m-1'><span class='material-icons align-middle'>delete</span></button>";
                echo "</td>";

                echo "</tr>";
            }

            echo "</tbody>";
            echo "</table>";
            if ($total_productos == 0) {
                echo "<p class='text-center'>Aún no se han registrado productos</p>";
            } else {
                echo "<p>$total_productos productos registrados</p>";
            }

            echo "</div>";
            ?>
        </div>

        <!-- pagination -->
        <nav class="mt-5 mb-5">
            <ul class="pagination justify-content-center">
                <li class="page-item">
                    <a class="page-link" <?php echo ($pagina_actual > 1) ? "href='?pagina=$pagina_anterior'" : "" ?>><i
                            class="fa fa-chevron-left"></i></a>
                </li>
                <?php
                for ($x = 1; $x <= $total_paginas; $x++) {
                ?>
                <li class="page-item"><a class="page-link" href="?pagina=<?php echo $x ?>"><?php echo $x; ?></a></li>
                <?php
                }
                ?>
                <li class="page-item">
                    <a class="page-link"
                        <?php echo ($pagina_actual < $total_paginas) ? "href='?pagina=$pagina_siguiente'" : "" ?>><i
                            class="fa fa-chevron-right"></i></a>
                </li>
            </ul>
        </nav>

        <!-- Modal Edit-->
        <div class='modal fade' style="top:58px !important;" id='editModal' tabindex='-1'
            aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog' style="margin-bottom:100px !important;">
                <div class='modal-content'>
                    <div class='modal-header'>
                        <h5 class='modal-title' id='exampleModalLabel'>Editar producto</h5>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>

                    <?php
                    $sql = "SELECT * FROM producto";
                    $query = mysqli_query($db, $sql);
                    $productos = mysqli_fetch_array($query);
                    ?>

                    <form action='edit.php' method='POST'>
                        <div class='modal-body text-start'>
                            <input type='hidden' name='editCodeProducto' id='edit_id'>
                            <div class="col-12 mb-3">
                                <label for="editNmbreProducto" class="form-label">Editar nombre:</label>
                                <input type="text" id="edit_nombre" name="editNmbreProducto" class="form-control"
                                    placeholder="Laptop" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="editPriceProducto" class="form-label">Editar precio:</label>
                                <input type="number" id="edit_precio" name="editPriceProducto" class="form-control"
                                    placeholder="150000" required>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="editCantidadProducto" class="form-label">Cantidad producto</label>
                                <input type="number" id="edit_cantidad" name="editCantidadProducto" class="form-control"
                                    placeholder="10" required>
                            </div>

                            <div class='modal-footer flex justify-content-center'>
                                <button type="reset" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i><span class="ms-2">Limpiar</span>
                                </button>
                                <button type='submit' name='edit_data' class='btn btn-primary'>Actualizar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Modal Delete-->
        <div class='modal fade' style="top:58px !important;" id='deleteModal' tabindex='-1'
            aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>


                    <form action='deleteProduct.php' method='POST'>

                        <div class='modal-body text-start'>
                            <input type='hidden' name='delete_id' id='delete_id'>
                            <p>¿Desea eliminar este producto?</p>
                        </div>

                        <div class='modal-footer'>
                            <button type='submit' name='deletedata' class='btn btn-primary'>Sí</button>
                        </div>

                    </form>


                </div>
            </div>
        </div>


        <!-- tutup container -->
    </div>


    <!-- Jquery -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <!-- Javascript bule with popper bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ygbV9kiqUc6oa4msXn9868pTtWMgiQaeYH7/t7LECLbyPA2x65Kgf80OJFdroafW" crossorigin="anonymous">
    </script>

    <!-- sweet alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

    <!-- edit function -->
    <script>
    $(document).ready(function() {
        $('.editButton').on('click', function() {
            $('#editModal').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            console.log(data);
            $('#edit_id').val(data[0]);
            $('#edit_nombre').val(data[1]);
            $('#edit_precio').val(data[2]);
            $('#edit_cantidad').val(data[3]);
        });
    });
    </script>

    <!-- delete function -->
    <script>
    $(document).ready(function() {
        $('.deleteButton').on('click', function() {
            $('#deleteModal').modal('show');

            $tr = $(this).closest('tr');

            var data = $tr.children("td").map(function() {
                return $(this).text();
            }).get();

            console.log(data);
            $('#delete_id').val(data[0]);
        });
    });
    </script>

    <script>
    function clicking() {
        window.location.href = './index.php';
    }
    </script>
</body>

</html>