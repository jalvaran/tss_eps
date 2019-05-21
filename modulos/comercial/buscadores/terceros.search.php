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

$sql = "SELECT * FROM clientes 
		WHERE RazonSocial LIKE '%$key%' or Num_Identificacion LIKE '%$key%' OR  Telefono LIKE '%$key%'
		LIMIT 50"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['RazonSocial']." ".$row['Num_Identificacion']." ".$row['Telefono'];
     $json[] = ['id'=>$row['Num_Identificacion'], 'text'=>$Texto];
}
echo json_encode($json);