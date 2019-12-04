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
$obRest=new conexion($idUser);
$key=$obRest->normalizar($_REQUEST['q']);
$nit=$obRest->normalizar($_REQUEST['nit']);

$sql = "SELECT * FROM contratos 
		WHERE (NumeroContrato LIKE '%$key%' AND NitIPSContratada = '$nit') 
		LIMIT 200"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['Contrato']." | ".$row['NitIPSContratada']." | ".$row['ClasificacionContrato']." | ".number_format($row['ValorContrato'])." | ".$row['FechaInicioContrato']." | ".$row['FechaFinalContrato'];
     $json[] = ['id'=>$row['ID'], 'text'=>$Texto];
}
echo json_encode($json);