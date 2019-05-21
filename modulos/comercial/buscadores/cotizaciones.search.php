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

$sql = "SELECT ct.ID,cl.RazonSocial,cl.Num_Identificacion,cl.Telefono FROM cotizacionesv5 ct INNER JOIN clientes cl ON cl.idClientes=ct.Clientes_idClientes 
		WHERE cl.RazonSocial LIKE '%$key%' or ct.ID = '$key' or cl.Telefono like '%$key%' or cl.Num_Identificacion='$key'
		LIMIT 50"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['ID']." // ".$row['RazonSocial']." // ".$row['Num_Identificacion']." // ".$row['Telefono'];
     $json[] = ['id'=>$row['ID'], 'text'=>$Texto];
}
echo json_encode($json);