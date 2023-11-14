<?php
session_start();
include '../koneksi.php';

$harga = $_POST['harga'];
mysqli_query($koneksi, "UPDATE harga SET harga_per_kilo='$harga'");
header("location: harga.php?pesan=berhasil");
?>
