<?php
ob_start();
session_start();
if (!isset($_SESSION['username'])){
  exit("<a href='../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$idUser=$_SESSION['idUser'];
$TipoUser=$_SESSION['tipouser'];

include_once("../modelo/php_tablas.php");  //Clases con el contenido del manejo de las tablas
$obCon = new conexion($idUser);
$VectorPermisos["Page"]=$myPage;
/* 
 * Aqui se revisan los permisos segun el tipo de usuario
 */
$Permiso=$obCon->VerificaPermisos($VectorPermisos);
//$Permiso=1;
if ($Permiso==0){
  exit("<a href='../index.php' ><img src='../images/401.png'>Iniciar Sesion </a>");
  
}
$tabLog="log_pages_visits";
$Datos["Fecha"]=date("Y-m-d H:i:s");
$Datos["idUser"]=$_SESSION['idUser'];                        
$Datos["IP_Conexion"]=$_SERVER['REMOTE_ADDR'];
$Datos["Page"]=$myPage;
$sql=$obCon->getSQLInsert($tabLog, $Datos);
$obCon->Query($sql);
?>