<?php
include("./config.php");

// cek apa tombol daftar udah di klik blom
if (isset($_POST['edit_data'])) {
    // ambil data dari form
    $id = $_POST['editCodeProducto'];
    $nombre = $_POST['editNmbreProducto'];
    $precio = $_POST['editPriceProducto'];
    $cantidad = $_POST['editCantidadProducto'];


    // query$sql = "UPDATE mahasiswa SET nama='$nama', NIM='$NIM', jenis_kelamin='$jenis_kelamin', jurusan='$jurusan', agama='$agama', IPK='$IPK' WHERE id = '$id'";
    
    
    $sql = "UPDATE Producto 
        SET nombre_producto='$nombre', 
            precio='$precio', 
            cantidad_disponible='$cantidad' 
        WHERE codigo_producto = '$id'";

    $query = mysqli_query($db, $sql);

    // cek apa query berhasil disimpan?
    if ($query)
        header('Location: ./index.php?update=exito');
    else
        header('Location: ./index.php?update=error');
} else
    die("Akses dilarang...");

?>