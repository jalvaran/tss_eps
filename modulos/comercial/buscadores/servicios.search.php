<?php

include_once("../../../modelo/php_conexion.php");
session_start();
$idUser=$_SESSION['idUser'];
if($idUser==''){
    $json[0]['id']="";
    $json[0]['text']="Debe iniciar sesion para realizar la busqueda";
    echo json_encode($json);
    exit();
}
$obRest=new ProcesoVenta($idUser);
$key=$obRest->normalizar($_REQUEST['q']);

$sql = "SELECT * FROM servicios 
		WHERE Nombre LIKE '%$key%' or idProductosVenta = '$key' OR  Referencia = '$key'
		LIMIT 50"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['Nombre']." // ".$row['idProductosVenta']." // ".$row['Referencia']." // ".number_format($row['PrecioVenta']);
     $json[] = ['id'=>$row['idProductosVenta'], 'text'=>$Texto];
}
echo json_encode($json);