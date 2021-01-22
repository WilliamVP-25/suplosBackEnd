<?php
include('./conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
  return false;
}

// capturar datos enviados desde ajax
$id = htmlspecialchars($_POST['Id']);
$direccion = htmlspecialchars($_POST['Direccion']);
$precio = htmlspecialchars($_POST['Precio']);
$ciudad = htmlspecialchars($_POST['Ciudad']);
$codigo_postal = htmlspecialchars($_POST['Codigo_Postal']);
$tipo = htmlspecialchars($_POST['Tipo']);
$telefono = htmlspecialchars($_POST['Telefono']);

/**
* formatear precio para eliminar caracteres no admisibles como número
*/
$precio = str_replace("$", "", $precio);
$precio = str_replace(",", "", $precio);

$consulta_ciudad = mysqli_query($conexion, "SELECT id FROM ciudades WHERE nombre='$ciudad'");
$row = mysqli_fetch_array($consulta_ciudad);
$ciudad_id = $row['id'];

if (!$ciudad_id) {
    echo "error:no_existe_ciudad_seleccionada";
    return;
}

$consulta_tipo = mysqli_query($conexion, "SELECT id FROM tipos WHERE nombre='$tipo'");
$row = mysqli_fetch_array($consulta_tipo);
$tipo_id = $row['id'];

if (!$tipo_id) {
    echo "error:no_existe_tipo_seleccionado";
    exit;
}

$consulta_bien = mysqli_query($conexion, "SELECT id FROM bienes WHERE id='$id'");
if (mysqli_num_rows($consulta_bien) > 0) {
    echo "duplicado";
    exit;
}

$insert = "INSERT INTO bienes VALUES ($id,'$direccion','$telefono','$codigo_postal', $precio,'$ciudad_id','$tipo_id')";
/**
* @return {string} respuesta de operación insert
*/
if (mysqli_query($conexion, $insert)) {
    echo 'success';
} else {
    echo 'error';
}