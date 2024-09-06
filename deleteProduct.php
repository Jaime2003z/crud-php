<?php
include("./config.php");

if (isset($_POST['deletedata'])) {
    $id = $_POST['delete_id'];

    // query 
    $sql = "DELETE FROM producto WHERE codigo_producto = '$id'";
    $query = mysqli_query($db, $sql);

    if ($query) {
        header('Location: ./index.php?hapus=exito');
    } else
        die('Location: ./index.php?hapus=error');
} else
    die("Acceso prohibido...");

?>