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

$sql = "SELECT * FROM cod_municipios_dptos 
		WHERE Ciudad LIKE '%$key%' or Departamento LIKE '%$key%' 
		LIMIT 10"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['Ciudad']." ".$row['Departamento'];
     $json[] = ['id'=>$row['Ciudad'], 'text'=>$Texto];
}
echo json_encode($json);