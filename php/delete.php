<?php
include('./conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
  return false;
}

/**
* eliminar bienes en base de datos
* @param {int} Id bien a eliminar
*/
$id = $_POST['bienId'];

$delete = "DELETE FROM bienes WHERE id='$id'";

/**
* eliminar bienes en base de datos
* @return {string} resultado operación eliminado
*/
if (mysqli_query($conexion, $delete)) {
    echo 'success';
} else {
    echo 'error';
}