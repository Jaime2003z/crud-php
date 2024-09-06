<?php

$server = "localhost";
$user = "root";
$password = "";
$nama_database = "crudPhp";

$db = mysqli_connect($server, $user, $password, $nama_database);

if (!$db)
    die("error terhubung dengan database: " . mysqli_connect_error());
?>