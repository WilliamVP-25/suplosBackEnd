<?php
/**
* parámetros de conexión
*/
$servidor = 'localhost';
$usuario = 'root';
$pw = '';
$basedatos = 'Intelcost_bienes';

$conexion = mysqli_connect($servidor, $usuario, $pw)
or die("No se puede conectar con el servidor $servidor del motor de base de datos ");
$db = mysqli_select_db($conexion, $basedatos);
$conexion->set_charset("utf8");
