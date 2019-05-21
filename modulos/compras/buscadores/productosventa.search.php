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

$sql = "SELECT * FROM productosventa pv INNER JOIN prod_codbarras cod ON cod.ProductosVenta_idProductosVenta=pv.idProductosVenta
		WHERE pv.Nombre LIKE '%$key%' or pv.idProductosVenta = '$key' OR  pv.Referencia = '$key' OR cod.CodigoBarras like '%$key%'
		LIMIT 50"; 
$result = $obRest->Query($sql);
$json = [];

while($row = $obRest->FetchAssoc($result)){
    $Texto=$row['Nombre']." // ".$row['idProductosVenta']." // ".$row['Referencia']." // ".number_format($row['CostoUnitario'])." // ".number_format($row['Existencias']);
     $json[] = ['id'=>$row['idProductosVenta'], 'text'=>$Texto];
}
echo json_encode($json);