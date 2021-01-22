<?php
include('./conexion.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
  return false;
}

require("../vendor/autoload.php");
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

$documento = new Spreadsheet();
$documento
    ->getProperties()
    ->setCreator("wavp25@gmail.com")
    ->setLastModifiedBy('wavp25@gmail.com') // última vez modificado por
    ->setTitle('Reporte Bienes')
    ->setSubject('Reporte Bienes')
    ->setDescription('')
    ->setKeywords('')
    ->setCategory('');

$hoja = $documento->getActiveSheet();
//nombre de la hoja
$hoja->setTitle("Reporte Bienes");
//encabezados
$hoja->setCellValueByColumnAndRow(1, 1, "ID");
$hoja->setCellValueByColumnAndRow(2, 1, "DIRECCIÓN");
$hoja->setCellValueByColumnAndRow(3, 1, "CIUDAD");
$hoja->setCellValueByColumnAndRow(4, 1, "TELÉFONO");
$hoja->setCellValueByColumnAndRow(5, 1, "CÓDIGO POSTAL");
$hoja->setCellValueByColumnAndRow(6, 1, "TIPO");
$hoja->setCellValueByColumnAndRow(7, 1, "PRECIO");
//estilo a encabezados
$documento->getActiveSheet()->getStyle('A1:G1')->getFont()->setBold(true);
$documento->getActiveSheet()->getStyle('A1:G1')->getAlignment()->setHorizontal('center');

//recibir filtros
$ciudad = $_REQUEST['ciudad'];
$tipo = $_REQUEST['tipo'];

//consultar id de ciudad y tipo
$consulta_ciudad = mysqli_query($conexion, "SELECT id FROM ciudades WHERE nombre='$ciudad'");
$row = mysqli_fetch_array($consulta_ciudad);
$ciudad_id = $row['id'];

$consulta_tipo = mysqli_query($conexion, "SELECT id FROM tipos WHERE nombre='$tipo'");
$row = mysqli_fetch_array($consulta_tipo);
$tipo_id = $row['id'];

//consultar en base de datos segun filtros
$consulta_bienes = mysqli_query($conexion,
    "SELECT
       bienes.*, 
       ciudades.nombre as nombre_ciudad,
       tipos.nombre as nombre_tipo
    FROM bienes 
    INNER JOIN ciudades ON ciudades.id = bienes.ciudad 
    INNER JOIN tipos ON tipos.id = bienes.tipo
    WHERE bienes.ciudad='$ciudad_id' AND bienes.tipo='$tipo_id'
    ");

if (mysqli_num_rows($consulta_bienes) < 0) {
    echo "no_results";
    exit;
}

$data = array();
for ($i = 2; $row = mysqli_fetch_array($consulta_bienes); $i++) {
    //mostrar información de los bienes filtrados en la celdas
    $hoja->setCellValue("A" . $i, $row['id']);
    $hoja->setCellValue("B" . $i, $row['direccion']);
    $hoja->setCellValue("C" . $i, $row['nombre_ciudad']);
    $hoja->setCellValue("D" . $i, $row['telefono']);
    $hoja->setCellValue("E" . $i, $row['codigo_postal']);
    $hoja->setCellValue("F" . $i, $row['nombre_tipo']);
    $hoja->setCellValue("G" . $i, "$" . number_format($row['precio'], 0, '.', ','));
}
//ajustar tamaño al conteenido de la celda
$documento->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
$documento->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
$documento->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
$documento->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);


$nombreDelDocumento = "Reporte Bienes.xlsx";
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="' . $nombreDelDocumento . '"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($documento, 'Xlsx');
$writer->save('php://output');
exit;